<?php

namespace App\Livewire;

use Livewire\Component;

use App\Livewire\Forms\CreateAddressForm;
use Illuminate\Support\Facades\Http;

class ShippingAddresses extends Component
{
    public CreateAddressForm $form;
    public $addresses;
    public $newAddress = false;
    public $localities = [];

    public function mount()
    {
        $this->loadAddresses();
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

        // Si se elige una localidad, sugerimos que el barrio es el mismo (opcional)
        if ($value) {
            $this->form->district = $value;
        }
    }

    public function loadAddresses()
    {
        $this->addresses = auth()->user()->addresses()->orderBy('is_default', 'desc')->get();
    }

    public function edit($id)
    {
        $address = auth()->user()->addresses()->find($id);
        $this->form->setAddress($address);
        $this->updatedFormProvince($address->province); // Cargar localidades para el select
        $this->form->locality = $address->locality; // Restaurar localidad
        $this->newAddress = true;
    }

    public function delete($id)
    {
        auth()->user()->addresses()->where('id', $id)->delete();
        $this->loadAddresses();
        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Dirección eliminada',
            'text' => 'La dirección ha sido removida de tu cuenta.',
            'confirmButtonColor' => '#7c3aed',
        ]);
    }

    public function save()
    {
        $isEdit = !empty($this->form->addressId);
        $result = $this->form->save();

        $this->newAddress = false;
        $this->loadAddresses();

        if ($isEdit && $result === 0) {
            $this->newAddress = false;
            $this->dispatch('swal', [
                'icon' => 'info',
                'title' => 'Sin cambios',
                'text' => 'No se detectaron cambios en la dirección. ¡Puedes continuar con tu compra!',
                'confirmButtonColor' => '#7c3aed',
            ]);
            return;
        }

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => $isEdit ? '¡Dirección actualizada!' : '¡Dirección guardada!',
            'text' => $isEdit ? 'Los cambios se han guardado correctamente.' : 'La dirección se ha registrado correctamente.',
            'confirmButtonColor' => '#7c3aed',
        ]);
    }

    public function render()
    {
        return view('livewire.shipping-addresses');
    }
}
