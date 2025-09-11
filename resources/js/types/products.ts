export interface ProductImage {
    id: number;
    product_id: number;
    image_path: string;
    is_primary: true;
}

export interface ProductInterface {
    id: number;
    limit_name: string;
    name: string;
    description: string;
    category: string;
    price: number;
    slug: string;
    weight: number;
    stock: number;
    is_active: number;
    created_at: string;
    product_images: ProductImage[];
}
