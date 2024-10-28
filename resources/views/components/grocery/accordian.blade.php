@props([
'category' => '',
'categories' => '',
'parent_id' => '',
'id' =>'',
])


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
<div x-data="categoryHandler()" class="space-y-4 p-4" x-init="initializeTinyMCE()">
    <!-- General Accordion -->
    <div class="border rounded-lg shadow-sm">
        <button type="button" @click="openPanel = openPanel === 'general' ? null : 'general'" class="w-full text-left px-4 py-3 bg-indigo-500 text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-t-lg">
            General
        </button>

        <div x-show="openPanel === 'general'" x-collapse>
            <div class="px-4 py-4 bg-white">
                <!-- Form fields for General information -->
                <input type="hidden" id="id" name="id" value="{{  $category->id ?? '' }}" />
                <div class="mb-4">
                    <label for="categoryName" class="form-label block text-sm font-medium text-gray-700">Category Name</label>
                    <input type="text" class="form-control mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="categoryName" name="categoryName" value="{{ old('name', $category->name ?? '') }}">
                </div>
                <div class="mb-4">
                    <label for="description" class="form-label block text-sm font-medium text-gray-700">Description</label>
                    <textarea class="form-control tinymce mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="description" name="description">{{ old('description', $category->description ?? '') }}</textarea>
                </div>
                <div class="mb-4">
                    <label for="shortDescription" class="form-label block text-sm font-medium text-gray-700">Short Description</label>
                    <textarea class="form-control mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="shortDescription" name="shortDescription">{{ old('shortDescription', $category->short_description ?? '') }}</textarea>
                </div>

                <div class="mb-4">
                    <label for="parentCategory" class="form-label block text-sm font-medium text-gray-700">Parent Category</label>
                    <select class="form-select mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="parentCategory" name="parentCategory" @change="fetchSubcategories($event)">
                        <option value="">Select Parent Category</option>
                        @foreach($categories as $parentCategory)
                        <option value="{{ $parentCategory->id }}" {{ ($parent_id && $parent_id == $parentCategory->id)|| (isset($category) && optional($category)->parent_id == $parentCategory->id)  || (old('parentCategory') == $parentCategory->id) ? 'selected' : '' }}>
                            {{ $parentCategory->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="is_feature" class="form-label block text-sm font-medium text-gray-700">Featured Category</label>
                    <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                        <input type="checkbox" name="is_feature" id="is_feature" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer" {{ old('is_feature', $category->is_feature ?? false) ? 'checked' : '' }}>
                        <label for="is_feature" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Accordion -->
    <div class="border rounded-lg shadow-sm">
        <button type="button" @click="openPanel = openPanel === 'image' ? null : 'image'" class="w-full text-left px-4 py-3 bg-indigo-500 text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-t-lg">
            Image
        </button>
        <div x-show="openPanel === 'image'" x-collapse>
            <div class="px-4 py-4 bg-white">
                <!-- Form fields for Image -->

                <div class="mb-4" id="simpleImage">
                    <label for="properImage" class="form-label block text-sm font-medium text-gray-700">Feature Image</label>

                    <!-- Show old image if it exists -->
                    @if(!empty($category->feature_image))
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $category->feature_image) }}" alt="Feature Image" class="w-24 h-24 object-cover">
                    </div>
                    @endif

                    <input type="file" class="form-control mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" name="featureImage" >
                </div>

                @if($parent_id)
                <div class="mb-4">
                    <label for="thumbnailImage" class="form-label block text-sm font-medium text-gray-700">Thumbnail Image</label>

                    <!-- Show old thumbnail image if it exists -->
                    @if(!empty($category->thumbnail_image))
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $category->thumbnail_image) }}" alt="Thumbnail Image" class="w-24 h-24 object-cover">
                    </div>
                    @endif

                    <input type="file" class="form-control mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="thumbnailImage" name="thumbnailImage" >
                </div>
                @else
                <div class="mb-4">
                    <label for="thumbnailImage" class="form-label block text-sm font-medium text-gray-700">Thumbnail Image</label>

                    <!-- Show old thumbnail image if it exists -->
                    @if(!empty($category->thumbnail_image))
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $category->thumbnail_image) }}" alt="Thumbnail Image" class="w-24 h-24 object-cover">
                    </div>
                    @endif

                    <input type="file" class="form-control mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="thumbnailImage" name="thumbnailImage" >
                </div>

                <div class="mb-4">
                    <label for="iconImage" class="form-label block text-sm font-medium text-gray-700">Icon Image</label>

                    <!-- Show old icon image if it exists -->
                    @if(!empty($category->icon_image))
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $category->icon_image) }}" alt="Icon Image" class="w-24 h-24 object-cover">
                    </div>
                    @endif

                    <input type="file" class="form-control mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="iconImage" name="iconImage" >
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- SEO Accordion -->
    <div class="border rounded-lg shadow-sm">
        <button type="button" @click="openPanel = openPanel === 'seo' ? null : 'seo'" class="w-full text-left px-4 py-3 bg-indigo-500 text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-t-lg">
            SEO
        </button>
        <div x-show="openPanel === 'seo'" x-collapse>
            <div class="px-4 py-4 bg-white">
                <!-- Form fields for SEO -->
                <div class="mb-4">
                    <label for="metaTitle" class="form-label block text-sm font-medium text-gray-700">Meta Title</label>
                    <input type="text" class="form-control mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="metaTitle" name="metaTitle" value="{{ old('metaTitle', $category->meta_title ?? '') }}">
                </div>
                <div class="mb-4">
                    <label for="metaDescription" class="form-label block text-sm font-medium text-gray-700">Meta Description</label>
                    <textarea class="form-control mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="metaDescription" name="metaDescription">{{ old('metaDescription', $category->meta_description ?? '') }}</textarea>
                </div>
                <div class="mb-4">
                    <label for="metaKeywords" class="form-label block text-sm font-medium text-gray-700">Meta Keywords</label>
                    <input type="text" class="form-control mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="metaKeywords" name="metaKeywords" value="{{ old('metaKeywords', $category->meta_keywords ?? '') }}">
                </div>
                <div class="mb-4">
                    <label for="metaUrl" class="form-label block text-sm font-medium text-gray-700">Meta Url</label>
                    <input type="text" class="form-control mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="metaUrl" name="metaUrl" value="{{ old('metaUrl', $category->meta_url ?? '') }}">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function initializeTinyMCE() {
        tinymce.init({
            selector: '#description, ',
            plugins: 'lists link image table',
            toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | table',
            height: 300
        });
    }


    function categoryHandler() {
        return {
            openPanel: null,
            subcategories: [],
            async fetchSubcategories(event) {
                let categoryId = event.target.value;
                if (categoryId) {
                    try {
                        let response = await fetch(`/admin/subcategories/${categoryId}`);
                        if (response.ok) {
                            this.subcategories = await response.json();
                        } else {
                            console.error('Error fetching subcategories');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                } else {
                    this.subcategories = [];
                }
            }
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        const featuredCategoryCheckbox = document.getElementById("is_feature");
        const featureImageSection = document.getElementById("simpleImage"); // Assuming the "Feature Image" input has ID "simpleImage"

        featuredCategoryCheckbox.addEventListener("change", function() {
            if (featuredCategoryCheckbox.checked) {
                featureImageSection.style.display = "block"; // Show if checkbox is checked
            } else {
                featureImageSection.style.display = "none"; // Hide if checkbox is unchecked
            }
        });

        // Initial check (hide the section if checkbox is not checked on page load)
        if (!featuredCategoryCheckbox.checked) {
            featureImageSection.style.display = "none";
        }
    });
    initializeTinyMCE();
</script>