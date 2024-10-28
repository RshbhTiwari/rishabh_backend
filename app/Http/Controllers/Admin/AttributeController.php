<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Attribute;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;

class AttributeController extends Controller
{
    public function addEditAttribute(string $id): View
    {
        $attribute = $id === 'new' ? new Attribute() : Attribute::findOrFail($id);

        return view('admin.attribute-form', ['id' => $id])->with(compact(['attribute']));
    }

    public function saveAttribute(Request $request): RedirectResponse
    {
        Validator::make($request->input(), [
            'name' => 'required|string|max:255',
        ])->validate();

        Attribute::updateOrCreate(
            ['id' => $request->id],
            [
                'name' => $request->name,
            ]
        );

        $message = $request->id ? 'Attribute updated' : 'New attribute added';

        return redirect()->route('attributes')->with('status', $message);

    }

    public function deleteAttribute(string $id): RedirectResponse
    {
        $attribute = Attribute::findOrFail($id);
        $attribute->delete();

        return redirect()->route('attributes')->with('status', 'Attribute deleted successfully!');
    }

}
