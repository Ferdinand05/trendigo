<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { LayoutGrid, ListOrderedIcon, Package, PackageSearch, Settings2, Shield, Users } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import NavFooter from './NavFooter.vue';
import NavUser from './NavUser.vue';
const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
];

const UserNavItems: NavItem[] = [
    {
        title: 'Users',
        href: '/users',
        icon: Users,
    },
    {
        title: 'Roles',
        href: '/roles',
        icon: Shield,
    },
];

const ProductNavItems: NavItem[] = [
    {
        title: 'Products',
        href: '/products',
        icon: Package,
    },
    {
        title: 'Categories',
        href: '/categories',
        icon: PackageSearch,
    },
];

const OrderNavItems: NavItem[] = [
    {
        title: 'Orders',
        href: '/dashboard/orders',
        icon: ListOrderedIcon,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Store Setting',
        href: '/settings/shop',
        icon: Settings2,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" headerItems="Platform" />
            <NavMain :items="UserNavItems" headerItems="User" />
            <NavMain :items="ProductNavItems" headerItems="Product" />
            <NavMain :items="OrderNavItems" headerItems="Order" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
