<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Batik - Measurements</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Poppins', 'sans-serif'],
                        'dancing': ['Dancing Script', 'cursive'],
                    }
                }
            }
        }
    </script>
    <style>
        .font-dancing {
            font-family: 'Dancing Script', cursive;
        }
        .size-guide-table th, .size-guide-table td {
            text-align: center;
            padding: 0.75rem;
            border: 1px solid #e5e7eb;
            transition: background-color 0.3s ease;
        }
        .size-guide-table th {
            font-weight: 600;
        }
        .size-guide-table .size-col {
            font-weight: 600;
        }
        .size-guide-table tr.selected {
            background-color: #e0f2fe;
        }
        .focus\:ring-cyan-400:focus {
            --tw-ring-color: #22d3ee;
        }
        .text-cyan-600 {
            color: #0891b2;
        }
        .custom-size-input {
            width: 60px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 4px;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-800 pb-28">

    <x-header />
    <main class="w-full max-w-7xl mx-auto bg-white rounded-2xl shadow-lg p-6 sm:p-10 mt-8 mb-8">
        <div class="mb-8">
            <h2 class="text-3xl font-bold mb-2">Measurements</h2>
            <p class="text-gray-600">Enter your measurements to ensure your batik clothing fits perfectly. All measurements should be in centimeters (cm).</p>
        </div>

        <hr class="my-6">
        
        <h3 class="text-2xl font-bold mb-6">Standard Measurement</h3>

        <form action="{{ route('order.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
                
                <div class="flex flex-col">
                    <div>
                        <h4 class="text-xl font-bold mb-4">Choose Your Size</h4>
                        <div id="size-options" class="space-y-3">
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                <input type="radio" name="size" value="XS" class="h-5 w-5 text-cyan-600 focus:ring-cyan-500" checked>
                                <span class="ml-4 text-lg">XS (Extra Small)</span>
                            </label>
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                <input type="radio" name="size" value="S" class="h-5 w-5 text-cyan-600 focus:ring-cyan-500">
                                <span class="ml-4 text-lg">S (Small)</span>
                            </label>
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                <input type="radio" name="size" value="M" class="h-5 w-5 text-cyan-600 focus:ring-cyan-500">
                                <span class="ml-4 text-lg">M (Medium)</span>
                            </label>
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                <input type="radio" name="size" value="L" class="h-5 w-5 text-cyan-600 focus:ring-cyan-500">
                                <span class="ml-4 text-lg">L (Large)</span>
                            </label>
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                <input type="radio" name="size" value="XL" class="h-5 w-5 text-cyan-600 focus:ring-cyan-500">
                                <span class="ml-4 text-lg">XL (Extra Large)</span>
                            </label>
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                <input type="radio" name="size" value="custom" class="h-5 w-5 text-cyan-600 focus:ring-cyan-500">
                                <span class="ml-4 text-lg">Custom Size</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-xl font-bold">Size Guide</h4>
                        <button type="button" id="garment-toggle-btn" class="text-cyan-600 font-semibold flex items-center gap-2">
                            <span id="garment-type-text">Shirt</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                        </button>
                    </div>
                    
                    <div id="shirt-size-table" class="overflow-x-auto">
                        <div class="mb-6">
                            

[Image of a shirt measurement guide]

                            <img 
                                id="shirt-guide-image" 
                                src="images/shirt_guide.png" 
                                alt="Shirt Measurement Guide" 
                                class="w-full max-w-sm mx-auto rounded-lg shadow-sm h-80 md:h-96 object-contain"
                            >
                        </div>
                        <table class="w-full border-collapse size-guide-table">
                            <thead>
                                <tr>
                                    <th class="bg-gray-50">Size</th>
                                    <th class="bg-red-100 text-red-800">Body Length</th>
                                    <th class="bg-orange-100 text-orange-800">Sleeve Length</th>
                                    <th class="bg-blue-100 text-blue-800">Shoulder Width</th>
                                    <th class="bg-green-100 text-green-800">Body Width</th>
                                    <th class="bg-black text-white">Neck Size</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr data-size="XS"><td class="size-col">XS</td><td>69</td><td>78.5</td><td>43.5</td><td>51</td><td>39</td></tr>
                                <tr data-size="S"><td class="size-col">S</td><td>71</td><td>80.5</td><td>45</td><td>54</td><td>39</td></tr>
                                <tr data-size="M"><td class="size-col">M</td><td>73</td><td>83</td><td>46.5</td><td>57</td><td>41</td></tr>
                                <tr data-size="L"><td class="size-col">L</td><td>75</td><td>85.5</td><td>48</td><td>60</td><td>43</td></tr>
                                <tr data-size="XL"><td class="size-col">XL</td><td>78</td><td>88</td><td>50</td><td>64</td><td>45</td></tr>
                                <tr data-size="custom" style="display: none;"><td class="size-col">Custom</td>
                                    <td><input type="number" name="custom_body_length" class="custom-size-input"></td>
                                    <td><input type="number" name="custom_sleeve_length" class="custom-size-input"></td>
                                    <td><input type="number" name="custom_shoulder_width" class="custom-size-input"></td>
                                    <td><input type="number" name="custom_body_width" class="custom-size-input"></td>
                                    <td><input type="number" name="custom_neck_size" class="custom-size-input"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div id="dress-size-table" class="overflow-x-auto" style="display: none;">
                        <div class="mb-6">
                            
                            <img 
                                id="dress-guide-image" 
                                src="images/dress_guide.png" 
                                alt="Dress Measurement Guide" 
                                class="w-full max-w-sm mx-auto rounded-lg shadow-sm h-80 md:h-96 object-contain"
                            >
                        </div>
                        <table class="w-full border-collapse size-guide-table">
                            <thead>
                                <tr>
                                    <th class="bg-gray-50">Size</th>
                                    <th class="bg-red-100 text-red-800">Body Length</th>
                                    <th class="bg-orange-100 text-orange-800">Sleeve Length</th>
                                    <th class="bg-blue-100 text-blue-800">Shoulder Width</th>
                                    <th class="bg-green-100 text-green-800">Body Width</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr data-size="XS"><td class="size-col">XS</td><td>110</td><td>60</td><td>38</td><td>48</td></tr>
                                <tr data-size="S"><td class="size-col">S</td><td>112</td><td>61</td><td>39</td><td>51</td></tr>
                                <tr data-size="M"><td class="size-col">M</td><td>114</td><td>62</td><td>40</td><td>54</td></tr>
                                <tr data-size="L"><td class="size-col">L</td><td>116</td><td>63</td><td>41</td><td>57</td></tr>
                                <tr data-size="XL"><td class="size-col">XL</td><td>118</td><td>64</td><td>42</td><td>60</td></tr>
                                <tr data-size="custom" style="display: none;"><td class="size-col">Custom</td>
                                    <td><input type="number" name="custom_dress_body_length" class="custom-size-input"></td>
                                    <td><input type="number" name="custom_dress_sleeve_length" class="custom-size-input"></td>
                                    <td><input type="number" name="custom_dress_shoulder_width" class="custom-size-input"></td>
                                    <td><input type="number" name="custom_dress_body_width" class="custom-size-input"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <input type="hidden" name="garment_type" id="garment-type-input" value="shirt">

                    <div id="preview-container" class="mt-6 text-center" style="display: none;">
                        <h4 class="text-lg font-bold mb-4 text-gray-700">Your Design Preview</h4>
                        <img id="image-preview" src="#" alt="Your uploaded design" class="max-w-xs w-full mx-auto rounded-lg shadow-md border">
                    </div>

                    <div class="mt-8">
                        <label for="design_file" class="block text-lg font-medium text-gray-700 mb-2">Upload Your Design (PNG)</label>
                        <input type="file" name="design_file" id="design_file" accept=".png, image/png" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                    </div>
                </div>
            </div>

            <div class="mt-10 flex flex-col-reverse gap-4 sm:flex-row sm:justify-between">
                <a href="/create" class="w-full sm:w-auto text-center bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-8 rounded-lg text-lg transition">Back to designing</a>
                <button type="submit" class="w-full sm:w-auto bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-3 px-8 rounded-lg text-lg transition shadow-md hover:shadow-lg">Continue Checkout</button>
            </div>
        </form>
    </main>


    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const sizeOptions = document.getElementById('size-options');
        const shirtTable = document.getElementById('shirt-size-table');
        const dressTable = document.getElementById('dress-size-table');
        const garmentToggleBtn = document.getElementById('garment-toggle-btn');
        const garmentTypeText = document.getElementById('garment-type-text');
        const designFileInput = document.getElementById('design_file');
        const imagePreview = document.getElementById('image-preview');
        const previewContainer = document.getElementById('preview-container');
        const customShirtRow = document.querySelector('#shirt-size-table tr[data-size="custom"]');
        const customDressRow = document.querySelector('#dress-size-table tr[data-size="custom"]');
        const garmentTypeInput = document.getElementById('garment-type-input');
        

        function highlightRow(selectedValue) {
            const activeTable = shirtTable.style.display !== 'none' ? shirtTable : dressTable;
            activeTable.querySelectorAll('tbody tr').forEach(row => {
                if(row.dataset.size !== 'custom'){
                    row.classList.toggle('selected', row.dataset.size === selectedValue);
                }
            });

            if(selectedValue === 'custom'){
                if(shirtTable.style.display !== 'none'){
                    customShirtRow.style.display = 'table-row';
                } else {
                    customDressRow.style.display = 'table-row';
                }
            } else {
                customShirtRow.style.display = 'none';
                customDressRow.style.display = 'none';
            }
        }

        sizeOptions.addEventListener('change', (e) => {
            if (e.target.name === 'size') {
                highlightRow(e.target.value);
            }
        });
        
        garmentToggleBtn.addEventListener('click', () => {
            const isShirtVisible = shirtTable.style.display !== 'none';

            if (isShirtVisible) {
                shirtTable.style.display = 'none';
                dressTable.style.display = 'block';
                garmentTypeText.textContent = 'Dress';
                garmentTypeInput.value = 'dress';
            } else {
                shirtTable.style.display = 'block';
                dressTable.style.display = 'none';
                garmentTypeText.textContent = 'Shirt';
                garmentTypeInput.value = 'shirt';
            }
            
            const selectedRadio = document.querySelector('input[name="size"]:checked');
            if(selectedRadio){
                highlightRow(selectedRadio.value);
            }
        });

        designFileInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                
                reader.onload = (e) => {
                    imagePreview.src = e.target.result;
                    previewContainer.style.display = 'block';
                };
                
                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
                imagePreview.src = '#';
            }
        });

        const initialSelected = document.querySelector('input[name="size"]:checked');
        if (initialSelected) {
            highlightRow(initialSelected.value);
        }
    });
    </script>
</body>
</html>