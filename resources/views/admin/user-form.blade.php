<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl">{{ $id === 'new' ? 'Add New User' : 'Edit User' }}</h2>
    </x-slot>

    <div class="bg-skin-page shadow rounded-lg px-4 pt-1 pb-2">
        <form method="POST" action="{{ route('user.save') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="id" name="id" value="{{  $user->id ?? '' }}" />
            <div class="space-y-4 sm:space-y-3">
                <div>
                    <label for="name" class="block text-gray-700">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="w-full mt-1 p-2 border border-gray-300 rounded-md">
                </div>

                <div>
                    <label for="email" class="block text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="w-full mt-1 p-2 border border-gray-300 rounded-md">
                </div>

                <div>
                    <label for="contact" class="block text-gray-700">contact</label>
                    <input type="contact" name="contact" id="contact" value="{{ old('contact', $user->contact) }}" class="w-full mt-1 p-2 border border-gray-300 rounded-md">
                </div>

                <div>
                    <label for="pincode" class="block text-gray-700">pincode</label>
                    <input type="pincode" name="pincode" id="pincode" value="{{ old('pincode', $user->pincode) }}" class="w-full mt-1 p-2 border border-gray-300 rounded-md">
                </div>
                <input type="hidden" name="id" value="{{ $user->id }}">
                <div>
                    <label for="city" class="block text-gray-700">city</label>
                    <input type="city" name="city" id="city" value="{{ old('city', $user->city) }}" class="w-full mt-1 p-2 border border-gray-300 rounded-md">
                </div>

                <div>
                    <label for="password" class="block text-gray-700">Password</label>
                    <input type="password" name="password" id="password" class="w-full mt-1 p-2 border border-gray-300 rounded-md">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-gray-700">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="w-full mt-1 p-2 border border-gray-300 rounded-md">
                </div>
            </div>

            <div class="mb-12">&nbsp;</div>
            <div class="fixed bottom-0 left-0 md:left-40 right-0">
                <div class="px-4 py-3 ml-4 sm:px-6 border-l border-skin-primary bg-gradient-to-t from-white">
                    <x-button class="h-10 w-full">{{ __('Save') }}</x-button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
