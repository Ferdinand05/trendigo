export interface ShippingAddress {
    recipient_name: string;
    phone: string;
    address: string;
    province: string;
    city: string;
    district: string;
    sub_district: string;
    postal_code: string;
}

export interface OrderItem {
    id: number;
    product_id: number;
    product: string;
    quantity: number;
    price: number;
    subtotal: number;
    notes: string;
}

export interface Order {
    id: number;
    order_code: string;
    user_id: number;
    user: {
        name: string;
        email: string;
    };
    shipping_address_id: number;
    shipping_address: ShippingAddress;
    shipping_cost: number;
    shipping_service: string;
    shipping_etd: string;
    shipping_name: string;
    order_status: string;
    subtotal: number;
    total: number;
    status: string;
    midtrans_transaction_id: string | null;
    midtrans_payment_type: string | null;
    midtrans_transaction_status: string | null;
    midtrans_response: any; // kalau respon dari Midtrans bentuknya objek bisa diganti ke Record<string, any>
    fraud_status: string | null;
    created_at: string; // format 'd-m-y H:i:s'
    order_items: OrderItem[];
    midtrans_snap_token: string;
}
