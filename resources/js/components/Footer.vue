<script setup lang="ts">
const mode = localStorage.getItem('appearance');
</script>

<template>
    <footer class="border-t shadow-sm dark:bg-blue-800 dark:text-gray-200">
        <div class="mx-auto w-full max-w-screen-xl p-4 md:py-8">
            <div class="sm:flex sm:items-center sm:justify-between">
                <a href="/" class="mb-4 flex items-center space-x-3 sm:mb-0 rtl:space-x-reverse">
                    <img :src="mode !== 'dark' ? '/img/trendigo-logo.svg' : '/img/trendigo-logo-dark.svg'" class="h-20 md:h-24" alt="Trendigo Logo" />
                    <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-gray-200">Trendigo</span>
                </a>
                <div class="ml-4 md:ml-0">
                    <ul class="mb-6 flex flex-wrap items-center text-sm font-medium text-gray-500 dark:text-gray-200">
                        <li class="hover:text-blue-500">
                            <a href="#" class="me-4 hover:underline md:me-6">Tentang</a>
                        </li>
                        <li class="hover:text-blue-500">
                            <a href="#" class="me-4 hover:underline md:me-6">Produk</a>
                        </li>
                        <li class="hover:text-blue-500">
                            <a href="#" class="me-4 hover:underline md:me-6">Terms & Condition</a>
                        </li>
                        <li class="hover:text-blue-500">
                            <a href="#" class="hover:underline">Kontak</a>
                        </li>
                    </ul>

                    <div class="flex gap-x-2 text-gray-500 md:gap-x-3.5 dark:text-gray-200">
                        <a href="https://instagram.com" target="_blank" rel="noopener noreferrer">
                            <img width="32" height="32" src="https://img.icons8.com/fluency/48/instagram-new.png" alt="instagram-new" />
                        </a>
                        <a href="https://facebook.com" target="_blank" rel="noopener noreferrer">
                            <img width="32" height="32" src="https://img.icons8.com/color/48/facebook.png" alt="facebook" />
                        </a>
                        <a href="https://x.com" target="_blank" rel="noopener noreferrer">
                            <img width="32" height="32" src="https://img.icons8.com/color/48/twitterx--v1.png" alt="twitterx--v1" />
                        </a>
                    </div>
                </div>
            </div>
            <hr class="my-6 border-gray-200 sm:mx-auto lg:my-4 dark:border-gray-700" />
            <span class="block text-sm text-gray-400 sm:text-center dark:text-gray-200"
                >© 2025 <a href="/" class="hover:underline">Trendigo</a>. All Rights Reserved.</span
            >
            <div class="text-sm text-gray-400 md:text-center dark:text-gray-300">Ferdinand. created with ❤️</div>
        </div>
    </footer>
</template>
