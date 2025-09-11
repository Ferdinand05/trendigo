import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';
import { ProductInterface } from './products';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

interface Category {
    id: string;
    name: string;
    slug: string;
    description: string;
    description_limit: string;
    created_at: string;
}

interface CartProduct {
    id: number;
    user_id: number;
    product: ProductInterface;
    quantity: number;
    subtotal: number;
    created_at: string;
    notes?: string;
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    flash: {
        message: string;
    };
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
    categories: {
        data: Category[];
    };
    cartProduct: {
        data: CartProduct[];
    };
    shippingAddress: {
        id: number;
        user_id: number;
        recipient_name: string;
        phone: string;
        address: string;
        province: string;
        province_id: number | null;
        city: string;
        city_id: number | null;
        district: string;
        district_id: number | null;
        sub_district: string;
        sub_district_id: number | null;
        postal_code: string;
    };
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;
