<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class CreateAddressForm extends Form
{
    public $addressId;
    public $receiver = '1'; // 1: Yo, 2: Otra persona

    #[Validate('required')]
    public $type = 'Hogar';

    #[Validate('nullable|string')]
    public $description;

    #[Validate('required')]
    public $province;

    #[Validate('required')]
    public $locality;

    #[Validate('required')]
    public $zip_code;

    #[Validate('required|string')]
    public $address;

    #[Validate('nullable|string')]
    public $district;

    #[Validate('nullable|string')]
    public $apartment;

    #[Validate('nullable|string')]
    public $reference;

    #[Validate('required|string')]
    public $contact;

    #[Validate('required|string')]
    public $phone;

    public function setAddress($address)
    {
        $this->addressId = $address->id;
        $this->type = $address->type;
        $this->description = $address->description;
        $this->province = $address->province;
        $this->locality = $address->locality;
        $this->zip_code = $address->zip_code;
        $this->district = $address->district;
        $this->address = $address->address;
        $this->apartment = $address->apartment;
        $this->reference = $address->reference;
        $this->contact = $address->contact;
        $this->phone = $address->phone;
        
        // Detectar si el contacto es el usuario o no
        $this->receiver = ($address->contact == auth()->user()->name) ? '1' : '2';
    }

    public function save()
    {
        if ($this->receiver == '1') {
            $this->contact = auth()->user()->name;
        }

        $this->validate();

        $data = [
            'type' => $this->type,
            'description' => $this->description,
            'province' => $this->province,
            'locality' => $this->locality,
            'zip_code' => $this->zip_code,
            'district' => $this->district,
            'address' => $this->address,
            'apartment' => $this->apartment,
            'reference' => $this->reference,
            'contact' => $this->contact,
            'phone' => $this->phone,
        ];

        if ($this->addressId) {
            $updated = auth()->user()->addresses()->where('id', $this->addressId)->update($data);
            $this->reset();
            return $updated;
        } else {
            $is_default = auth()->user()->addresses->count() == 0;
            $address = auth()->user()->addresses()->create($data + ['is_default' => $is_default]);
            $this->reset();
            return $address;
        }
    }
}
