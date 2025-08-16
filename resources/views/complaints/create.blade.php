<x-app-layout>
    <x-slot name="title">
        {{ __('Submit Complaint') }}
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ __('Submit a New Complaint') }}
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-zinc-400">
                    {{ __('Please provide detailed information about your complaint. This will help us address your concern more effectively.') }}
                </p>
            </div>

            <div class="bg-white dark:bg-zinc-800 shadow rounded-lg">
                <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf

                    <!-- Category Selection -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">
                            {{ __('Category') }} <span class="text-red-500">*</span>
                        </label>
                        <select id="category" name="category" required 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-zinc-600 dark:bg-zinc-700 dark:text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">{{ __('Select a category') }}</option>
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">
                            {{ __('Description') }} <span class="text-red-500">*</span>
                        </label>
                        <textarea id="description" name="description" rows="6" required
                                  class="mt-1 block w-full border-gray-300 dark:border-zinc-600 dark:bg-zinc-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                  placeholder="{{ __('Please provide a detailed description of your complaint...') }}">{{ old('description') }}</textarea>
                        <p class="mt-2 text-sm text-gray-500 dark:text-zinc-400">
                            {{ __('Minimum 10 characters required.') }}
                        </p>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Photo Upload -->
                    <div>
                        <label for="photo" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">
                            {{ __('Photo (Optional)') }}
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-zinc-600 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-zinc-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600 dark:text-zinc-400">
                                    <label for="photo" class="relative cursor-pointer bg-white dark:bg-zinc-700 rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>{{ __('Upload a file') }}</span>
                                        <input id="photo" name="photo" type="file" class="sr-only" accept="image/*">
                                    </label>
                                    <p class="pl-1">{{ __('or drag and drop') }}</p>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-zinc-400">
                                    PNG, JPG, GIF up to 2MB
                                </p>
                            </div>
                        </div>
                        @error('photo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('complaints.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-zinc-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-zinc-300 bg-white dark:bg-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            {{ __('Submit Complaint') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Information Box -->
            <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                            {{ __('What happens next?') }}
                        </h3>
                        <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>{{ __('Your complaint will be automatically assigned to the appropriate department') }}</li>
                                <li>{{ __('You will receive updates on the status of your complaint') }}</li>
                                <li>{{ __('You can provide feedback once your complaint is resolved') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
