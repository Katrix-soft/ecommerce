<?php

namespace App\Livewire;

use Livewire\Component;
use App\Livewire\Forms\CreateAddressForm;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Variant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Gloudemans\Shoppingcart\Facades\Cart;
use Livewire\WithFileUploads;

class Checkout extends Component
{
    use WithFileUploads;

    public CreateAddressForm $form;
    
    // Transfer details
    public $transfer_issuer_name;
    public $transfer_issuer_cuit;
    public $transfer_receipt;

    
    // Wizard Steps: 1 = Shipping, 2 = Payment, 3 = Confirmation/Success
    public $step = 1;
    
    // Shipping Address variables
    public $addresses = [];
    public $selectedAddressId = null;
    public $newAddress = false;
    public $localities = [];
    
    // Payment variables
    public $paymentMethod = 'mercadopago'; // mercadopago, bank_transfer, cash
    
    // Mercado Pago payment data
    public $mpPaymentId = null;
    public $mpPaymentStatus = null;
    
    // Completed Order reference
    public $createdOrder = null;

    protected $listeners = [
        'addressSelected' => 'selectAddress',
        'mpPaymentApproved' => 'placeOrderWithMP',
    ];

    public function mount()
    {
        // Si el carrito está vacío y no estamos en confirmación, redirigir
        if (Cart::instance('shopping')->count() == 0 && $this->step != 3) {
            return redirect()->route('cart.index');
        }

        $this->loadAddresses();
        
        // Auto-seleccionar dirección predeterminada
        $defaultAddress = collect($this->addresses)->firstWhere('is_default', true);
        if ($defaultAddress) {
            $this->selectedAddressId = $defaultAddress->id;
        } elseif (count($this->addresses) > 0) {
            $this->selectedAddressId = $this->addresses[0]->id;
        }
    }

    public function loadAddresses()
    {
        $this->addresses = auth()->user()->addresses()->orderBy('is_default', 'desc')->get();
    }

    public function selectAddress($id)
    {
        $this->selectedAddressId = $id;
    }

    public function updatedFormProvince($value)
    {
        if ($value) {
            $response = Http::get("https://apis.datos.gob.ar/georef/api/localidades", [
                'provincia' => $value,
                'campos' => 'id,nombre',
                'max' => 1000
            ]);

            if ($response->successful()) {
                $this->localities = collect($response->json()['localidades'])->sortBy('nombre')->toArray();
            }
        } else {
            $this->localities = [];
        }
        $this->form->locality = '';
        $this->form->zip_code = '';
    }

    public function updatedFormLocality($value)
    {
        $suggestedCp = \App\Services\ArgentineLocations::getZipCode($this->form->province, $value);
        
        if ($suggestedCp) {
            $this->form->zip_code = $suggestedCp;
        }

        if ($value) {
            $this->form->district = $value;
        }
    }

    public function edit($id)
    {
        $address = auth()->user()->addresses()->find($id);
        $this->form->setAddress($address);
        $this->updatedFormProvince($address->province);
        $this->form->locality = $address->locality;
        $this->newAddress = true;
    }

    public function delete($id)
    {
        auth()->user()->addresses()->where('id', $id)->delete();
        $this->loadAddresses();
        
        if ($this->selectedAddressId == $id) {
            $this->selectedAddressId = count($this->addresses) > 0 ? $this->addresses[0]->id : null;
        }

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Dirección eliminada',
            'text' => 'La dirección ha sido removida.',
            'confirmButtonColor' => '#7c3aed',
        ]);
    }

    public function saveAddress()
    {
        $isEdit = !empty($this->form->addressId);
        $result = $this->form->save();

        $this->newAddress = false;
        $this->loadAddresses();

        if ($result && isset($result->id)) {
            $this->selectedAddressId = $result->id;
        }

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => $isEdit ? '¡Dirección actualizada!' : '¡Dirección guardada!',
            'text' => 'Se ha guardado la dirección correctamente.',
            'confirmButtonColor' => '#7c3aed',
        ]);
    }

    public function goToPayment()
    {
        $cart = Cart::instance('shopping')->content();

        if ($cart->count() == 0) {
            $this->dispatch('swal', [
                'icon' => 'warning',
                'title' => 'Carrito vacío',
                'text' => 'Debes tener productos en tu carrito para continuar.',
                'confirmButtonColor' => '#7c3aed',
            ]);
            return;
        }

        $itemIds = $cart->pluck('id')->toArray();
        $stocks = Variant::whereIn('id', $itemIds)->pluck('stock', 'id')->toArray();
        $hasValidItems = false;

        foreach ($cart as $item) {
            $stock = $stocks[$item->id] ?? 0;
            if ($item->qty <= $stock) {
                $hasValidItems = true;
                break;
            }
        }

        if (!$hasValidItems) {
            return redirect()->route('cart.index');
        }

        if (!$this->selectedAddressId) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Dirección requerida',
                'text' => 'Por favor, selecciona o agrega una dirección de envío.',
                'confirmButtonColor' => '#7c3aed',
            ]);
            return;
        }

        $this->step = 2;
    }

    public function backToShipping()
    {
        $this->step = 1;
    }

    /**
     * Procesar orden con pago de Mercado Pago (llamado desde evento JS)
     */
    public function placeOrderWithMP($mpPaymentId, $mpStatus, $mpPaymentType = null)
    {
        $this->mpPaymentId = $mpPaymentId;
        $this->mpPaymentStatus = $mpStatus;
        $this->paymentMethod = 'mercadopago' . ($mpPaymentType ? '_' . $mpPaymentType : '');
        $this->placeOrder();
    }

    public function placeOrder()
    {
        if ($this->paymentMethod === 'bank_transfer') {
            $this->validate([
                'transfer_issuer_name' => 'required|string|max:255',
                'transfer_issuer_cuit' => 'required|string|max:255',
                'transfer_receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            ], [
                'transfer_issuer_name.required' => 'El nombre del titular es obligatorio.',
                'transfer_issuer_cuit.required' => 'El CUIT/CUIL es obligatorio.',
                'transfer_receipt.required' => 'Debes adjuntar un comprobante válido.',
                'transfer_receipt.mimes' => 'El comprobante debe ser una imagen o PDF.',
                'transfer_receipt.max' => 'El comprobante no debe superar los 5MB.',
            ]);
        }


        $tenant = \App\Models\User::getTenant();
        if ($tenant) {
            $ordersThisMonth = \App\Models\Order::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
            if ($ordersThisMonth >= $tenant->max_orders_per_month) {
                $this->dispatch('swal', [
                    'icon' => 'error',
                    'title' => 'Límite de Pedidos Excedido',
                    'text' => 'Lo sentimos, esta tienda ha superado su límite mensual de pedidos permitidos por su plan. Por favor, intente más tarde o contacte al administrador.',
                    'confirmButtonColor' => '#7c3aed',
                ]);
                return;
            }
        }

        // Obtener dirección seleccionada
        $addressObj = Address::find($this->selectedAddressId);
        if (!$addressObj) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error de Dirección',
                'text' => 'No pudimos encontrar la dirección seleccionada.',
                'confirmButtonColor' => '#7c3aed',
            ]);
            return;
        }

        // Crear snapshot de dirección para el pedido
        $addressSnapshot = [
            'type' => $addressObj->type,
            'description' => $addressObj->description,
            'province' => $addressObj->province,
            'locality' => $addressObj->locality,
            'zip_code' => $addressObj->zip_code,
            'district' => $addressObj->district,
            'address' => $addressObj->address,
            'apartment' => $addressObj->apartment,
            'reference' => $addressObj->reference,
            'contact' => $addressObj->contact,
            'phone' => $addressObj->phone,
        ];

        // Transacción atómica: validar stock con bloqueo pesimista, crear orden y descontar stock
        try {
            $order = DB::transaction(function () use ($addressSnapshot) {
                $cartContent = Cart::instance('shopping')->content();
                $itemIds = $cartContent->pluck('id')->toArray();

                // Bloqueo pesimista: bloquea las filas de variantes para evitar sobreventas concurrentes
                $variants = Variant::whereIn('id', $itemIds)->lockForUpdate()->get()->keyBy('id');

                // Validar stock real bajo bloqueo
                $validItems = [];
                $subtotalVal = 0;

                foreach ($cartContent as $item) {
                    $variant = $variants->get($item->id);
                    $stock = $variant ? $variant->stock : 0;

                    if ($item->qty <= $stock) {
                        $validItems[] = $item;
                        $subtotalVal += $item->qty * $item->price;
                    }
                }

                if (empty($validItems)) {
                    throw new \Exception('NO_STOCK');
                }

                $totalVal = $subtotalVal;
                $shippingCostVal = 0.00; // Envío Gratis

                // Determinar estado de pago según método
                $paymentStatus = 'pending';
                if ($this->paymentMethod === 'mercadopago' && $this->mpPaymentStatus === 'approved') {
                    $paymentStatus = 'paid';
                }

                // Crear pedido
                $order = Order::create([
                    'user_id' => auth()->id(),
                    'shipping_address' => $addressSnapshot,
                    'payment_method' => $this->paymentMethod,
                    'payment_status' => $paymentStatus,
                    'mp_payment_id' => $this->mpPaymentId,
                    'status' => 'pending',
                    'shipping_cost' => $shippingCostVal,
                    'subtotal' => $subtotalVal,
                    'total' => $totalVal,
                    'transfer_issuer_name' => $this->transfer_issuer_name,
                    'transfer_issuer_cuit' => $this->transfer_issuer_cuit,
                    'transfer_receipt_path' => $this->transfer_receipt ? $this->transfer_receipt->store('receipts', 'public') : null,
                ]);

                // Crear items del pedido y descontar stock atómicamente
                foreach ($validItems as $item) {
                    $features = [];
                    if ($item->options && isset($item->options->features)) {
                        $features = $item->options->features;
                    }

                    OrderItem::create([
                        'order_id' => $order->id,
                        'variant_id' => $item->id,
                        'name' => $item->name,
                        'quantity' => $item->qty,
                        'price' => $item->price,
                        'features' => $features,
                    ]);

                    // Descontar stock de forma atómica bajo el bloqueo pesimista
                    $variant = $variants->get($item->id);
                    if ($variant) {
                        $variant->decrement('stock', $item->qty);
                    }
                }

                return $order;
            });
        } catch (\Exception $e) {
            if ($e->getMessage() === 'NO_STOCK') {
                $this->dispatch('swal', [
                    'icon' => 'error',
                    'title' => 'No hay suficiente stock',
                    'text' => 'Lo sentimos, ninguno de los productos en tu carrito cuenta con stock disponible en este momento. Por favor, ajusta tu carrito.',
                    'confirmButtonColor' => '#7c3aed',
                ]);
                return;
            }
            throw $e;
        }

        // Limpiar carrito (fuera de la transacción DB ya que es el driver de sesión/shoppingcart)
        $cartContent = Cart::instance('shopping')->content();
        $rowIdsToRemove = [];
        foreach ($cartContent as $item) {
            // Remover los items que fueron procesados en la orden
            $wasOrdered = $order->items()->where('variant_id', $item->id)->exists();
            if ($wasOrdered) {
                $rowIdsToRemove[] = $item->rowId;
            }
        }
        foreach ($rowIdsToRemove as $rowId) {
            Cart::instance('shopping')->remove($rowId);
        }

        // Sincronizar el estado del carrito en la base de datos
        if (auth()->check()) {
            try {
                DB::table('shoppingcart')->where('identifier', auth()->id())->delete();
            } catch (\Exception $e) {
                // Ignorar
            }
            Cart::instance('shopping')->store(auth()->id());
        }

        $this->createdOrder = $order;
        $this->step = 3;

        $this->dispatch('cart-updated');
        
        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => '¡Compra completada con éxito!',
            'text' => 'Tu pedido se ha registrado bajo el código #' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
            'confirmButtonColor' => '#7c3aed',
        ]);
    }

    public function render()
    {
        $cart = Cart::instance('shopping')->content();
        
        // Cargar todos los stocks de las variantes de forma masiva (para evitar N+1 queries)
        $itemIds = $cart->pluck('id')->toArray();
        $stocks = Variant::whereIn('id', $itemIds)->pluck('stock', 'id')->toArray();

        $subtotalVal = 0;
        $hasStockErrors = false;
        $hasValidItems = false;

        foreach ($cart as $item) {
            $stock = $stocks[$item->id] ?? 0;
            if ($item->qty <= $stock) {
                $subtotalVal += $item->qty * $item->price;
                $hasValidItems = true;
            } else {
                $hasStockErrors = true;
            }
        }

        return view('livewire.checkout', [
            'stocks' => $stocks,
            'hasStockErrors' => $hasStockErrors,
            'hasValidItems' => $hasValidItems,
            'subtotal' => $subtotalVal,
            'total' => $subtotalVal,
            'totalAmount' => $subtotalVal,
            'mpPublicKey' => config('mercadopago.public_key'),
        ]);
    }
}
