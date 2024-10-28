<head>
    <!-- Ensure TinyMCE is included only once in your application -->
    <script src="https://cdn.tiny.cloud/1/tu7siagunczn0614xic4q8b3awtg04wwxe4w892ibzwlvum7/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        // Define a global function to initialize TinyMCE
        tinymce.init({
            selector: 'textarea.tinymce',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed linkchecker a11ychecker tinymcespellchecker permanentpen powerpaste advtable advcode editimage advtemplate ai mentions tinycomments tableofcontents footnotes mergetags autocorrect typography inlinecss markdown',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
            tinycomments_mode: 'embedded',
            tinycomments_author: 'Author name',
            mergetags_list: [{
                    value: 'First.Name',
                    title: 'First Name'
                },
                {
                    value: 'Email',
                    title: 'Email'
                },
            ],
            ai_request: (request, respondWith) => respondWith.string(() => Promise.reject("See docs to implement AI Assistant")),
        });
    </script>
</head>


<div x-data="{ openPanel: 'general' }" class="space-y-4 p-4">
    <!-- General Information Accordion -->
    <div class="border rounded-lg shadow-sm">
        <button type="button" @click="openPanel = openPanel === 'general' ? null : 'general'" class="w-full text-left px-4 py-3 bg-indigo-500 text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-t-lg">
            General Information
        </button>
        <div x-show="openPanel === 'general'" x-collapse>
            <div class="px-4 py-4 bg-white">
                <!-- Form fields for General information -->
                <div class="mb-4">
                    <label for="productName" class="form-label block text-sm font-medium text-gray-700">Product Name</label>
                    <input type="text" class="form-control mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="productName" name="productName" value="{{ old('productName', $product->name ?? '') }}">
                </div>
                <!-- <div class="mb-4">
                    <label for="productDescription" class="form-label block text-sm font-medium text-gray-700">Description</label>
                    <textarea class="form-control tinymce mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="productDescription" name="productDescription" >{{ old('productDescription', $product->description ?? '') }}</textarea>
                </div> -->
                <div class="mb-4">
                    <label for="productDescription" class="form-label block text-sm font-medium text-gray-700">Description</label>
                    <textarea class="form-control  mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="productDescription" name="productDescription">{{ old('productDescription', $product->description ?? '') }}</textarea>
                </div>

                <div class="mb-4">
                    <label for="shortdescription" class="form-label block text-sm font-medium text-gray-700">Short Description</label>
                    <input type="text" class="form-control mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="short_description" name="short_description" value="{{ old('short-description', $product->short_description ?? '') }}">
                </div>
                <div class="mb-4">
                    <label for="category" class="form-label block text-sm font-medium text-gray-700">Category</label>
                    <x-grocery.select-input class="h-56" label="Category" id="category_ids" name="category_ids[]" :data="$category" :multiple="true" :value="old('category_ids', isset($product) ? explode(',', $product->category_ids) : [])" />
                </div>
                <div class="mb-4">
                    <label for="brand" class="form-label block text-sm font-medium text-gray-700">Brand</label>
                    <input type="text" class="form-control mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="brand" name="brand" value="{{ old('brand', $product->brand ?? '') }}">
                </div>
                <div class="mb-4">
                    <label for="sku" class="form-label block text-sm font-medium text-gray-700">Sku</label>
                    <input type="text" class="form-control mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="sku" name="sku" value="{{ old('sku', $product->sku ?? '') }}">
                </div>
                <input type="hidden" id="productId" name="id" value="{{ $product->id ?? '' }}">

                <div class="mb-4">
                    <label for="is_feature" class="form-label block text-sm font-medium text-gray-700">Featured Product</label>
                    <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                        <input type="checkbox" name="is_feature" id="is_feature" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer" {{ old('is_feature', $category->is_feature ?? false) ? 'checked' : '' }}>
                        <label for="is_feature" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Pricing Accordion -->
    <div class="border rounded-lg shadow-sm">
        <button type="button" @click="openPanel = openPanel === 'pricing' ? null : 'pricing'" class="w-full text-left px-4 py-3 bg-indigo-500 text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-t-lg">
            Pricing
        </button>
        <div x-show="openPanel === 'pricing'" x-collapse>
            <div class="px-4 py-4 bg-white">
                <!-- Form fields for Pricing -->
                <div class="mb-4">
                    <label for="price" class="form-label block text-sm font-medium text-gray-700">Price</label>
                    <input type="number" step="0.01" class="form-control mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="price" name="price" value="{{ old('price', $product->price ?? '') }}">
                </div>
                <div class="mb-4">
                    <label for="discountPrice" class="form-label block text-sm font-medium text-gray-700">Discount Price</label>
                    <input type="number" step="0.01" class="form-control mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="discountPrice" name="discountPrice" value="{{ old('discountPrice', $product->discount_price ?? '') }}">
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Accordion -->
    <div class="border rounded-lg shadow-sm">
        <button type="button" @click="openPanel = openPanel === 'inventory' ? null : 'inventory'" class="w-full text-left px-4 py-3 bg-indigo-500 text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-t-lg">
            Inventory
        </button>
        <div x-show="openPanel === 'inventory'" x-collapse>
            <div class="px-4 py-4 bg-white">
                <!-- Form fields for Inventory -->
                <div class="mb-4">
                    <label for="stockQuantity" class="form-label block text-sm font-medium text-gray-700">Stock Quantity</label>
                    <input type="number" class="form-control mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="stockQuantity" name="stockQuantity" value="{{ old('stockQuantity', $product->stock_quantity ?? '') }}">
                </div>
                <div class="mb-4">
                    <label for="lowStockThreshold" class="form-label block text-sm font-medium text-gray-700">Low Stock Threshold</label>
                    <input type="number" class="form-control mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="lowStockThreshold" name="lowStockThreshold" value="{{ old('lowStockThreshold', $product->low_stock_threshold ?? '') }}">
                </div>
                <div class="mb-4">
                    <label for="stockStatus" class="form-label block text-sm font-medium text-gray-700">Stock Status</label>
                    <select class="form-select mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="stockStatus" name="stockStatus">
                        <option value="in_stock" {{ old('stockStatus', $product->stock_status ?? '') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="out_of_stock" {{ old('stockStatus', $product->stock_status ?? '') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="border rounded-lg shadow-sm">
        <button type="button" @click="openPanel = openPanel === 'images' ? null : 'images'" class="w-full text-left px-4 py-3 bg-indigo-500 text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-t-lg">
            Images
        </button>
        <div x-show="openPanel === 'images'" x-collapse>
            <div class="px-4 py-4 bg-white">
                <!-- Form fields for Images -->
                <div class="mb-4">
                    <label for="mainImage" class="form-label block text-sm font-medium text-gray-700">Feature Image</label>
                    <input type="file" class="form-control mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="mainImage" name="featureImage">
                    <!-- Display existing feature image -->
                    @if($product && $product->image)
                    <div class="mt-4">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="Feature Image" class="h-32 w-32 object-cover rounded-md">
                    </div>
                    @endif
                </div>

                <div class="mb-4">
                    <label for="additionalImages" class="form-label block text-sm font-medium text-gray-700">Additional Images</label>
                    <input type="file" multiple class="form-control mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="additionalImages" name="additionalImages[]">
                    <!-- Display existing additional images -->
                    @if($product && $product->additional_images)
                    <div class="mt-4 flex space-x-2">
                        @foreach(json_decode($product->additional_images) as $image)
                        <img src="{{ asset('storage/' . $image) }}" alt="Additional Image" class="h-20 w-20 object-cover rounded-md">
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Variant Accordion -->
    <div class="border rounded-lg shadow-sm">
        <button type="button" @click="openPanel = openPanel === 'variants' ? null : 'variants'" class="w-full text-left px-4 py-3 bg-indigo-500 text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-t-lg">
            Variants
        </button>
        <div x-show="openPanel === 'variants'" x-collapse>
            <div class="px-4 py-4 bg-white" x-data="{
            variants: {{ $variant->isNotEmpty() ? json_encode($variant) : json_encode([['attribute_id' => '', 'value' => '', 'stock' => '', 'price' => '']]) }},
            addVariant() {
                this.variants.push({ attribute_id: '', value: '', stock: '', price: '' });
            },
            removeVariant(index) {
                this.variants.splice(index, 1);
            }
        }">
                <template x-for="(variant, index) in variants" :key="index">
                    <div class="mb-4">
                        <label :for="'variant_' + index" class="block text-sm font-medium text-gray-700">Attributes</label>
                        <select :id="'variant_' + index" :name="'variants[' + index + '][attribute_id]'" x-model="variant.attribute_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select an attribute</option>
                            @foreach($attribute as $key => $val)
                            <option value="{{ $key }}" :selected="variant.attribute_id == '{{ $key }}'" x-text="'{{ $val }}'"></option>
                            @endforeach
                        </select>

                        <label :for="'variant_value_' + index" class="block text-sm font-medium text-gray-700 mt-4">Value</label>
                        <input type="text" :id="'variant_value_' + index" :name="'variants[' + index + '][value]'" placeholder="{{ __('Value') }}" x-model="variant.value" maxlength="255" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />

                        <label :for="'stock_value_' + index" class="block text-sm font-medium text-gray-700 mt-4">Stock Value</label>
                        <input type="text" :id="'stock_value_' + index" :name="'variants[' + index + '][stock]'" placeholder="{{ __('Stock Value') }}" x-model="variant.stock" maxlength="255" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />

                        <label :for="'variant_price_' + index" class="block text-sm font-medium text-gray-700 mt-4">Price</label>
                        <input type="text" :id="'variant_price_' + index" :name="'variants[' + index + '][price]'" placeholder="{{ __('Price') }}" x-model="variant.price" maxlength="255" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />

                        <label :for="'variant_discount_' + index" class="block text-sm font-medium text-gray-700 mt-4">Discount</label>
                        <input type="text" :id="'variant_discount_' + index" :name="'variants[' + index + '][discount]'" placeholder="{{ __('Discount') }}" x-model="variant.variant_discount" maxlength="255" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />

                        <label :for="'variant_discount_' + index" class="block text-sm font-medium text-gray-700 mt-4">Extension</label>
                        <input type="text" :id="'variant_extension_' + index" :name="'variants[' + index + '][extension]'" placeholder="{{ __('Extension') }}" x-model="variant.variant_extension" maxlength="255" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />

                        <input type="hidden" :id="'variant_id_' + index" :name="'variants[' + index + '][id]'" x-model="variant.id" />

                        <button type="button" @click="removeVariant(index)" class="mt-2 text-red-500">Remove Variant</button>
                    </div>
                </template>
                <button type="button" @click="addVariant()" class="mt-4 text-blue-500">Add Variant</button>
            </div>
        </div>
    </div>

    <div class="border rounded-lg shadow-sm">
        <button type="button" @click="openPanel = openPanel === 'seo' ? null : 'seo'" class="w-full text-left px-4 py-3 bg-indigo-500 text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-t-lg">
            SEO
        </button>
        <div x-show="openPanel === 'seo'" x-collapse>
            <div class="px-4 py-4 bg-white">
                <!-- Form fields for SEO -->
                <div class="mb-4">
                    <label for="metaTitle" class="form-label block text-sm font-medium text-gray-700">Meta Title</label>
                    <input type="text" class="form-control mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="metaTitle" name="metaTitle" value="{{ old('metaTitle', $product->meta_title ?? '') }}">
                </div>
                <div class="mb-4">
                    <label for="metaDescription" class="form-label block text-sm font-medium text-gray-700">Meta Description</label>
                    <textarea class="form-control mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="metaDescription" name="metaDescription">{{ old('metaDescription', $product->meta_description ?? '') }}</textarea>
                </div>
                <div class="mb-4">
                    <label for="metaKeywords" class="form-label block text-sm font-medium text-gray-700">Meta Keywords</label>
                    <input type="text" class="form-control mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="metaKeywords" name="metaKeywords" value="{{ old('metaKeywords', $product->meta_keywords ?? '') }}">
                </div>
                <div class="mb-4">
                    <label for="metaUrl" class="form-label block text-sm font-medium text-gray-700">Meta URL</label>
                    <input type="text" class="form-control mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="metaUrl" name="metaUrl" value="{{ old('metaUrl', $product->meta_url ?? '') }}">
                </div>
            </div>
        </div>
    </div>
</div>