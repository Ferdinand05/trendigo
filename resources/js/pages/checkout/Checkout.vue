<script setup lang="ts">
import Footer from '@/components/Footer.vue';
import Navbar from '@/components/Navbar.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import Separator from '@/components/ui/separator/Separator.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import { AppPageProps } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { LoaderCircle, Minus, Plus, Trash } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';
// const props = defineProps<{}>();
import axios from 'axios';
import Swal from 'sweetalert2';
import { Toaster } from 'vue-sonner';
import 'vue-sonner/style.css';
const page = usePage<AppPageProps>();

function formatRupiah(value: number) {
    return new Intl.NumberFormat('id-ID').format(value);
}

const cartTotal = computed((): number => {
    return page.props.cartProduct.data.reduce((sum: number, item) => sum + item.subtotal, 0);
});

const weightTotal = computed(() => {
    return page.props.cartProduct.data.reduce((sum: number, item) => sum + item.quantity * item.product.weight, 0);
});

function reduceQuantity(id: number): void {
    router.put(
        route('reduce.qty'),
        {
            id_cart: id,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                router.reload({ only: ['cartProduct'] });

                courier.value = '';
                selectedShippingCost.value = '';
            },
            onFinish: () => {},
        },
    );
}

function addQuantity(id: number): void {
    router.put(
        route('add.qty'),
        {
            id_cart: id,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                router.reload({ only: ['cartProduct'] });
                courier.value = '';
                selectedShippingCost.value = '';
            },

            onFinish: () => {},
        },
    );
}

function deleteCartItem(id: number) {
    router.delete(route('delete.cart.item'), {
        data: {
            id_cart: id,
        },
        preserveScroll: true,
        onSuccess: () => {
            router.reload({ only: ['cartProduct'] });

            courier.value = '';
            selectedShippingCost.value = '';
        },
        onFinish: () => {
            if (selectedShippingCost.value) {
                calculateDomesticCost();
            }
        },
    });
}

// shipping
interface Address {
    id: number;
    name: string;
    zip_code?: string | null;
}

const provinces = ref<Address[] | null>(null);
const selectedProvince = ref<number | null>(null);
watch(selectedProvince, (newVal: number | null) => {
    getCities(newVal);

    if (newVal && provinces.value) {
        const selected = provinces.value.find((prov) => prov.id == newVal);
        province.value = selected?.name ?? '';
    } else {
        province.value = '';
    }
});
const cities = ref<Address[] | null>();
const selectedCity = ref<number | null>(null);
watch(selectedCity, (newVal) => {
    getDistricts(newVal);

    if (newVal && cities.value) {
        const selected = cities.value.find((city) => city.id == newVal);
        city.value = selected?.name ?? '';
    } else {
        city.value = '';
    }
});

const districts = ref<Address[] | null>();
const selectedDistrict = ref<number | null>(null);
watch(selectedDistrict, (newVal) => {
    getSubDistricts(newVal);

    if (newVal && districts.value) {
        const selected = districts.value.find((dist) => dist.id == newVal);
        district.value = selected?.name ?? '';
    } else {
        district.value = '';
    }
});

const sub_districts = ref<Address[] | null>();
function getProvinces() {
    axios
        .get(route('get.provinces'))
        .then(function (response) {
            // handle success
            console.log(response.data.data);
            provinces.value = response.data.data;
        })
        .catch(function (error) {
            // handle error
            console.log(error);
        });
}
function getCities(province_id: number | null) {
    axios
        .get(route('get.cities', province_id))
        .then(function (response) {
            // handle success
            console.log(response.data.data);
            cities.value = response.data.data;
        })
        .catch(function (error) {
            // handle error
            console.log(error);
        });
}
function getDistricts(city_id: number | null) {
    axios
        .get(route('get.districts', city_id))
        .then(function (response) {
            // handle success
            console.log(response.data.data);
            districts.value = response.data.data;
        })
        .catch(function (error) {
            // handle error
            console.log(error);
        });
}
function getSubDistricts(district_id: number | null) {
    axios
        .get(route('get.sub.districts', district_id))
        .then(function (response) {
            // handle success
            console.log(response.data.data);
            sub_districts.value = response.data.data;
        })
        .catch(function (error) {
            // handle error
            console.log(error);
        });
}

const selectedSubDistrict = ref<number | null>(null);
watch(selectedSubDistrict, (newVal) => {
    if (newVal && sub_districts.value) {
        const selected = sub_districts.value.find((sub) => sub.id === newVal);
        postal_code.value = selected?.zip_code || '';
        sub_district.value = selected?.name ?? '';
    } else {
        postal_code.value = '';
        sub_district.value = '';
    }
});

onMounted(() => {
    getProvinces();

    if (page.props.cartProduct.data.length == 0) {
        router.visit(route('home'));
    }
});

type ShippingCost = {
    name: string;
    code?: string;
    service: string;
    cost: number;
    description: string;
    etd: string;
};

const recipient_name = ref<string>('');
const phone = ref<string>('');
const address = ref<string>('');
const province = ref<string>('');
const district = ref<string>('');
const sub_district = ref<string>('');
const city = ref<string>('');
const postal_code = ref<string>('');

const courier = ref<string | undefined>('');
const shippingCost = ref<ShippingCost[]>();
const selectedShippingCost = ref<string>('');
watch(courier, () => {
    calculateDomesticCost();
});

function calculateDomesticCost() {
    axios
        .post(route('calculate.cost'), {
            origin: 6166,
            destination: selectedDistrict.value,
            weight: weightTotal.value,
            courier: courier.value,
        })
        .then(function (response) {
            console.log(response);
            shippingCost.value = response.data.data;
        })
        .catch(function (error) {
            console.log(error);
        });
}

const dataSelectedShippingCost = ref<ShippingCost | null>(null);
watch(selectedShippingCost, (newVal) => {
    if (newVal && shippingCost.value) {
        const selected = shippingCost.value.find((data) => data.service == newVal);
        dataSelectedShippingCost.value = selected ?? null;
    } else {
        dataSelectedShippingCost.value = null;
    }
});

const disableButtonCheckout = ref<boolean>(false);
const createCharge = async () => {
    disableButtonCheckout.value = true;
    await axios
        .post(route('create.charge'), {
            name: recipient_name.value,
            email: page.props.auth.user.email,
            phone: phone.value,
            total: cartTotal.value,
            shipping_cost: dataSelectedShippingCost.value?.cost,
            weight: weightTotal.value,
            city: city.value,
            city_id: selectedCity.value,
            province: province.value,
            province_id: selectedProvince.value,
            district: district.value,
            district_id: selectedDistrict.value,
            sub_district: sub_district.value,
            sub_district_id: selectedSubDistrict.value,
            address: address.value,
            postal_code: postal_code.value,
            shipping_service_name: dataSelectedShippingCost.value?.name,
            shipping_etd: dataSelectedShippingCost.value?.etd,
            shipping_service: dataSelectedShippingCost.value?.service,
            // kirim cart items + notes
            items: page.props.cartProduct.data.map((item: any) => ({
                product_id: item.product.id,
                quantity: item.quantity,
                price: item.product.price,
                notes: item.notes ?? null,
            })),
        })
        .then(function (response: any) {
            const { snapToken, clientKey } = response.data;

            // Load Midtrans Snap.js
            if (!document.getElementById('midtrans-script')) {
                const script = document.createElement('script');
                script.src = 'https://app.sandbox.midtrans.com/snap/snap.js';
                script.setAttribute('data-client-key', clientKey);
                script.id = 'midtrans-script';
                document.body.appendChild(script);
            }

            // Tunggu script load
            setTimeout(() => {
                // @ts-expect-error snap token midtrans
                window.snap.pay(snapToken, {
                    onSuccess: function (result: any) {
                        console.log('success', result);

                        if (result.status_code == 200) {
                            Swal.fire({
                                title: 'Pembayaran berhasil!',
                                text: 'Pesanan akan segera di proses. Cek alur pesanan di profile.',
                                icon: 'success',
                                footer: '<a href="/user/profile/orders">Cek Pesanan!</a>',
                            });

                            emptyForm();
                        }
                    },
                    onPending: function (result: any) {
                        console.log(result);
                        Swal.fire({
                            title: 'Kamu keluar dari pembayaran',
                            text: 'Cek pembayaran yang belum selesai di Profil?',
                            icon: 'question',
                            footer: '<a href="/user/profile/orders">Cek Pesanan!</a>',
                        });
                    },
                    onError: function (result: any) {
                        console.log(result);
                        Swal.fire({
                            title: 'Pembayaran gagal?',
                            text: 'Cek informasi dan pastikan data benar',
                            icon: 'question',
                        });
                    },
                    onClose: function () {
                        Swal.fire({
                            title: 'Kamu menutup pembayaran',
                            text: 'Pembayaran gagal',
                            icon: 'question',
                        });
                    },
                });
            }, 500);
        })
        .catch(function (error: any) {
            console.log(error);
        })
        .finally(function () {
            disableButtonCheckout.value = false;
        });
};

function handelSnap() {
    Swal.fire({
        title: 'anda akan melakukan pembayaran',
        text: 'Pastikan data & informasi sesuai!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Bayar',
    }).then((result) => {
        if (result.isConfirmed) {
            createCharge();
        }
    });
}

//TODO fungsi kosongkan form
function emptyForm() {
    // data penerima
    recipient_name.value = '';
    phone.value = '';
    address.value = '';

    // alamat
    province.value = '';
    district.value = '';
    sub_district.value = '';
    city.value = '';
    postal_code.value = '';

    // selected dropdown id
    selectedProvince.value = null;
    selectedCity.value = null;
    selectedDistrict.value = null;
    selectedSubDistrict.value = null;

    // kurir dan ongkir
    courier.value = '';
    shippingCost.value = undefined;
    selectedShippingCost.value = '';
    dataSelectedShippingCost.value = null;
}
</script>

<template>
    <Head title="Halaman Checkout" />
    <Navbar />

    <Toaster />
    <div class="w-full">
        <img src="/img/pattern/waves1.svg" alt="" class="absolute w-full" />
    </div>
    <section class="relative mx-auto max-w-7xl px-6 py-10 pt-32 font-poppins sm:px-6 md:pt-36 lg:px-6 dark:text-gray-400">
        <div class="">
            <div class="mb-3">
                <h1 class="text-2xl font-bold md:text-3xl">Checkout</h1>
                <p class="font-light text-gray-600 md:text-lg">Isi informasi dan pastikan data yang dimasukan benar</p>
            </div>

            <!-- parent -->
            <form @submit.prevent="handelSnap()">
                <div class="flex flex-col p-2 md:flex-row md:gap-3">
                    <!-- list cart product -->
                    <div class="p-1 md:w-1/2">
                        <ul class="max-h-[950px] space-y-2 overflow-y-scroll p-2">
                            <li v-for="(cart, index) in page.props.cartProduct.data" :key="index" class="rounded-md border p-2">
                                <div>
                                    <div class="space-x-2">
                                        <span class="md:text-lg">
                                            {{ cart.product.name }}
                                        </span>
                                        <span class="text-muted-foreground">@{{ formatRupiah(cart.product.price) }}</span>
                                    </div>

                                    <div class="font-light text-gray-600">
                                        <div>Berat : {{ Number(cart.product.weight) * cart.quantity }} gram</div>
                                        <div>Jumlah : {{ cart.quantity }}</div>
                                        <div>Subtotal : Rp {{ formatRupiah(cart.subtotal) }}</div>
                                        <Textarea placeholder="Catatan..." v-model="cart.notes"></Textarea>
                                    </div>
                                    <div class="mt-2 flex gap-x-2">
                                        <Button @click.prevent="addQuantity(cart.id)" size="icon" class="size-7 cursor-pointer md:size-8">
                                            <Plus />
                                        </Button>
                                        <Button
                                            @click.prevent="reduceQuantity(cart.id)"
                                            size="icon"
                                            variant="outline"
                                            class="size-7 cursor-pointer md:size-8"
                                        >
                                            <Minus />
                                        </Button>
                                        <Button
                                            @click.prevent="deleteCartItem(cart.id)"
                                            size="icon"
                                            variant="destructive"
                                            class="size-7 cursor-pointer md:size-8"
                                        >
                                            <Trash />
                                        </Button>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <div class="mt-3">
                            <span class="text-lg font-semibold md:text-xl">Total : Rp {{ formatRupiah(cartTotal) }}</span>
                        </div>
                    </div>

                    <!-- form pengiriman -->
                    <div class="mt-5 md:mt-0 md:w-1/2">
                        <!-- form alamat -->
                        <div class="space-y-3.5">
                            <div class="">
                                <Label class="mb-1" for="nama_penerima">Nama Lengkap*</Label>
                                <Input id="nama_penerima" type="text" v-model="recipient_name" required />
                            </div>
                            <div class="">
                                <Label class="mb-1" for="no_telepon">No Telepon*</Label>
                                <Input id="no_telepon" type="text" v-model="phone" required />
                            </div>
                            <!-- provinsi -->
                            <div>
                                <Label class="mb-1" for="provinsi">Provinsi*</Label>
                                <Select id="provinsi" required v-model="selectedProvince" :disabled="provinces == null">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Pilih Provinsi" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectItem v-for="(p, i) in provinces" :value="p.id" :key="i"> {{ p.name }} </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                            </div>
                            <!-- kota & kecamatan -->
                            <div class="flex w-full justify-around gap-x-2">
                                <div class="w-full">
                                    <Label class="mb-1" for="kota">Kota*</Label>
                                    <Select id="kota" required v-model="selectedCity" :disabled="cities == null">
                                        <SelectTrigger class="w-full">
                                            <SelectValue placeholder="Pilih Kota" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectItem v-for="(c, i) in cities" :value="c.id" :key="i"> {{ c.name }} </SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div class="w-full">
                                    <Label class="mb-1" for="kecamatan">Kecamatan*</Label>
                                    <Select id="kecamatan" required v-model="selectedDistrict" :disabled="districts == null">
                                        <SelectTrigger class="w-full">
                                            <SelectValue placeholder="Pilih Kecamatan" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectItem v-for="(d, i) in districts" :value="d.id" :key="i"> {{ d.name }} </SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>
                            <div class="flex gap-x-2">
                                <div class="w-full">
                                    <Label class="mb-1" for="kelurahan">Kelurahan*</Label>
                                    <Select id="kelurahan" required v-model="selectedSubDistrict" :disabled="sub_districts == null">
                                        <SelectTrigger class="w-full">
                                            <SelectValue placeholder="Pilih Kelurahan" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectItem v-for="(s, i) in sub_districts" :key="i" :value="s.id"> {{ s.name }} </SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div class="w-full">
                                    <Label class="mb-1" for="kode_pos">Kode Pos</Label>
                                    <Input type="text " id="kode_pos" v-model="postal_code" readonly />
                                </div>
                            </div>
                            <div>
                                <Label class="mb-1" for="alamat_lengkap">Detail Alamat*</Label>
                                <Textarea
                                    v-model="address"
                                    id="alamat_lengkap"
                                    required
                                    autocomplete="off"
                                    autocorrect="off"
                                    placeholder="Alamat.."
                                ></Textarea>
                            </div>
                        </div>
                        <!-- end form alamat -->

                        <div class="mt-5 space-y-2.5 p-2">
                            <!-- berat & kurir -->
                            <div class="flex items-center gap-x-2">
                                <div class="w-full">
                                    <Label class="mb-1" for="berat">Berat (gram)</Label>
                                    <Input type="number" id="berat" readonly v-model="weightTotal" />
                                </div>
                                <div class="w-full">
                                    <Label class="mb-1" for="kurir">Ekspedisi</Label>
                                    <Select id="kurir" required v-model="courier" :disabled="selectedSubDistrict == null">
                                        <SelectTrigger class="w-full">
                                            <SelectValue placeholder="Pilih Kurir" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectItem value="jne"> JNE </SelectItem>
                                                <SelectItem value="jnt"> JNT </SelectItem>
                                                <SelectItem value="pos"> POS Indonesia </SelectItem>
                                                <SelectItem value="lion"> Lion Parcel</SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>
                            <div>
                                <div class="w-full">
                                    <Label class="mb-1" for="shipping_cost">Pilih Layanan</Label>
                                    <Select id="shipping_cost" required :disabled="shippingCost == null" v-model="selectedShippingCost">
                                        <SelectTrigger class="w-full">
                                            <SelectValue placeholder="Pilih Layananan" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectItem v-for="(shipping, i) in shippingCost" :value="shipping.service" :key="i">
                                                    {{ shipping.name }} - {{ shipping.description }}
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div class="py-3 italic">
                                    <h2 class="font-light">Detail Layanan</h2>
                                    <div class="font-medium" v-show="dataSelectedShippingCost !== null">
                                        <div>Deskripsi : {{ dataSelectedShippingCost?.description }} - {{ dataSelectedShippingCost?.service }}</div>
                                        <div>Harga : {{ dataSelectedShippingCost?.cost }}</div>
                                        <div>Estimasi Tiba : {{ dataSelectedShippingCost?.etd }}</div>
                                    </div>
                                </div>
                            </div>

                            <Separator />
                            <div class="md:mt-4">
                                <span class="text-lg font-semibold md:text-xl">
                                    Grand Total : Rp {{ formatRupiah(cartTotal + (dataSelectedShippingCost?.cost ?? 0)) }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-3 flex items-center justify-center">
                            <Button type="submit" class="hover:cursor-pointer" :disabled="disableButtonCheckout">
                                <LoaderCircle v-if="disableButtonCheckout" class="h-4 w-4 animate-spin" />
                                Bayar Sekarang
                            </Button>
                        </div>
                    </div>
                </div>
            </form>
            <!-- end parent -->
        </div>
    </section>

    <div class="mt-20">
        <Footer />
    </div>
</template>
