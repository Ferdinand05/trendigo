<script setup lang="ts">
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import {
    NavigationMenu,
    NavigationMenuContent,
    NavigationMenuItem,
    NavigationMenuLink,
    NavigationMenuList,
    NavigationMenuTrigger,
} from '@/components/ui/navigation-menu';
import { Link, usePage } from '@inertiajs/vue3';
import { LogIn, User2Icon } from 'lucide-vue-next';
import { onMounted, onUnmounted, ref } from 'vue';
import Cart from './Cart.vue';
const page = usePage();

const scrollY = ref(0);

const handleScroll = () => {
    scrollY.value = window.scrollY;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
    <nav
        class="fixed top-0 z-50 h-20 w-full transition-all duration-500"
        :class="{
            'bg-blue-800/85 backdrop-blur-md fade-in-10 dark:bg-blue-800 dark:backdrop-blur-none': scrollY > 50,
            'bg-transparent fade-out-10': scrollY <= 50,
        }"
    >
        <div class="mx-auto max-w-7xl px-6 sm:px-6 lg:px-6">
            <div class="flex h-20 items-center justify-between">
                <!-- Left: Logo + Kategori -->
                <div class="flex items-center space-x-4">
                    <!-- Logo -->
                    <Link href="/">
                        <img class="hidden h-14 w-auto object-cover md:block md:h-20" src="/img/trendigo-logo-dark.svg" alt="" />
                    </Link>

                    <Link href="/" class="rounded-sm px-2 py-1.5 text-[15px] font-medium text-gray-200 hover:text-gray-400" prefetch="hover">
                        Beranda
                    </Link>
                    <Link href="#product" class="rounded-sm px-2 py-1.5 text-[15px] font-medium text-gray-200 hover:text-gray-400" prefetch="hover">
                        Produk
                    </Link>

                    <!-- Contoh Navigation Menu -->
                    <div>
                        <NavigationMenu>
                            <NavigationMenuList>
                                <NavigationMenuItem>
                                    <NavigationMenuTrigger
                                        class="bg-transparent text-[15px] font-medium text-gray-200 hover:cursor-pointer hover:bg-transparent hover:text-gray-400"
                                        >Kategori</NavigationMenuTrigger
                                    >
                                    <NavigationMenuContent>
                                        <ul class="grid w-[320px] gap-3 overflow-y-scroll p-4 md:w-[500px] md:grid-cols-2 lg:w-[600px]">
                                            <li v-for="component in page.props.categories.data" :key="component.id">
                                                <NavigationMenuLink as-child>
                                                    <Link
                                                        prefetch="hover"
                                                        :href="route('product.in.category', component.slug)"
                                                        class="block space-y-1 rounded-md p-3 leading-none no-underline transition-colors outline-none select-none hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground"
                                                    >
                                                        <div class="text-sm leading-none font-medium">{{ component.name }}</div>
                                                        <p class="line-clamp-2 text-sm leading-snug text-muted-foreground">
                                                            {{ component.description }}
                                                        </p>
                                                    </Link>
                                                </NavigationMenuLink>
                                            </li>
                                        </ul>
                                    </NavigationMenuContent>
                                </NavigationMenuItem>
                            </NavigationMenuList>
                        </NavigationMenu>
                    </div>
                </div>

                <!-- Right: Account & Cart -->
                <div class="flex items-center space-x-5">
                    <div>
                        <DropdownMenu v-if="page.props.auth?.user?.name">
                            <DropdownMenuTrigger as-child>
                                <User2Icon :title="page.props.auth?.user?.name" class="text-gray-200 hover:scale-110 hover:cursor-pointer" />
                            </DropdownMenuTrigger>
                            <DropdownMenuContent class="mr-1.5 w-40">
                                <DropdownMenuItem>
                                    <Link :href="route('profile.user.index')" class="w-full">Profile</Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem>
                                    <Link :href="route('logout')" method="post" class="w-full text-left hover:cursor-pointer">Logout</Link>
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>

                        <Link href="/login" v-else>
                            <LogIn class="text-gray-200 hover:scale-110" />
                        </Link>
                    </div>

                    <!-- carts -->
                    <div v-show="page.props.auth.user?.name">
                        <Cart></Cart>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>
