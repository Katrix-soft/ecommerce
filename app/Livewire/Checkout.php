<?php

namespace App\Livewire;

use Livewire\Component;
use App\Livewire\Forms\CreateAddressForm;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Variant;
use Illuminate\Support\Facades\Http;
use Gloudemans\Shoppingcart\Facades\Cart;

class Checkout extends Component
{
    public CreateAddressForm $form;
    
    // Wizard Steps: 1 = Shipping, 2 = Payment, 3 = Confirmation/Success
    public $step = 1;
    
    // Shipping Address variables
    public $addresses = [];
    public $selectedAddressId = null;
    public $newAddress = false;
    public $localities = [];
    
    // Payment variables
    public $paymentMethod = 'credit_card'; // credit_card, bank_transfer, cash
    public $cardNumber = '';
    public $cardName = '';
    public $cardExpiry = '';
    public $cardCvv = '';
    
    // Completed Order reference
    public $createdOrder = null;

    protected $listeners = ['addressSelected' => 'selectAddress'];

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
        if (Cart::instance('shopping')->count() == 0) {
            $this->dispatch('swal', [
                'icon' => 'warning',
                'title' => 'Carrito vacío',
                'text' => 'Debes tener productos en tu carrito para continuar.',
                'confirmButtonColor' => '#7c3aed',
            ]);
            return;
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

    public function placeOrder()
    {
        // 1. Validar métodos de pago y campos de tarjeta si corresponde
        if ($this->paymentMethod === 'credit_card') {
            $this->validate([
                'cardNumber' => 'required|min:16',
                'cardName' => 'required|string|min:4',
                'cardExpiry' => 'required|regex:/^(0[1-9]|1[0-2])\/[0-9]{2}$/',
                'cardCvv' => 'required|numeric|digits_between:3,4',
            ], [
                'cardNumber.required' => 'El número de tarjeta es obligatorio.',
                'cardNumber.min' => 'El número de tarjeta debe tener al menos 16 dígitos.',
                'cardName.required' => 'El nombre del titular es obligatorio.',
                'cardName.min' => 'El nombre del titular debe ser completo.',
                'cardExpiry.required' => 'La fecha de vencimiento es obligatoria.',
                'cardExpiry.regex' => 'El formato de vencimiento debe ser MM/AA.',
                'cardCvv.required' => 'El código de seguridad (CVV) es obligatorio.',
                'cardCvv.numeric' => 'El CVV debe ser numérico.',
                'cardCvv.digits_between' => 'El CVV debe tener 3 o 4 dígitos.',
            ]);
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

        // Calcular montos finales
        $subtotalVal = floatval(str_replace(',', '', Cart::instance('shopping')->subtotal()));
        $totalVal = floatval(str_replace(',', '', Cart::instance('shopping')->total()));
        $shippingCostVal = 0.00; // Envío Gratis

        // Crear pedido
        $order = Order::create([
            'user_id' => auth()->id(),
            'shipping_address' => $addressSnapshot,
            'payment_method' => $this->paymentMethod,
            'payment_status' => $this->paymentMethod === 'credit_card' ? 'paid' : 'pending',
            'status' => 'pending',
            'shipping_cost' => $shippingCostVal,
            'subtotal' => $subtotalVal,
            'total' => $totalVal,
        ]);

        // Crear items del pedido y descontar stock si corresponde
        foreach (Cart::instance('shopping')->content() as $item) {
            // Guardar variantes o detalles
            $features = [];
            if ($item->options && isset($item->options->features)) {
                $features = $item->options->features;
            }

            OrderItem::create([
                'order_id' => $order->id,
                'variant_id' => $item->id, // row ID or variant ID? Let's check how cart handles id (usually variant ID)
                'name' => $item->name,
                'quantity' => $item->qty,
                'price' => $item->price,
                'features' => $features,
            ]);

            // Descontar stock
            $variant = Variant::find($item->id);
            if ($variant) {
                $variant->stock = max(0, $variant->stock - $item->qty);
                $variant->save();
            }
        }

        // Vaciar el carrito
        Cart::instance('shopping')->destroy();
        if (auth()->check()) {
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
        return view('livewire.checkout');
    }
}
