<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import Badge from '@/components/ui/badge/Badge.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Edit, Trash } from 'lucide-vue-next';
import { ref } from 'vue';
import { Toaster, toast } from 'vue-sonner';
import 'vue-sonner/style.css';
const breadcrumb: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users',
    },
];
type UserType = {
    id: number;
    name?: string;
    email?: string;
    role_id: number;
    created_at?: string;
    role_name?: string;
};

type RoleType = {
    id: number;
    role_name: string;
};

defineProps<{
    users: {
        data: UserType[];
    };
    roles: RoleType[];
}>();

const formCreateData = useForm({
    name: '',
    email: '',
    role_id: NaN,
    password: '',
    password_confirmation: '',
});

const createUser = (): void => {
    formCreateData.post(route('users.store'), {
        preserveScroll: true,
        onSuccess: (result: any) => {
            formCreateData.reset();
            toast('Success!', {
                description: `${result.props.flash.message}`,
                action: {
                    label: 'Close',
                    onClick: () => console.log('Closed'),
                },
            });
        },
    });
};

const formDeleteData = useForm({
    id: NaN,
});

const destroyUser = (id: number) => {
    formDeleteData.id = id;

    formDeleteData.delete(route('users.destroy', id), {
        preserveScroll: true,
        onSuccess: (result: any) => {
            toast('Deleted!', {
                description: `${result.props.flash.message}`,
                action: {
                    label: 'Close',
                    onClick: () => console.log('Closed'),
                },
            });
        },
    });
};
const modalDialogEdit = ref(false);
type formEditUserType = {
    put?: any;
    id?: number;
    name?: string;
    email?: string;
    errors?: any;
    role_id: number;
};
const formEditUser: formEditUserType = useForm({
    id: NaN,
    name: '',
    email: '',
    role_id: NaN,
});

// open dialog dan isi data dalam dialog
const fillDataModalDialog = (user: UserType) => {
    formEditUser.id = user.id;
    formEditUser.name = user.name;
    formEditUser.email = user.email;
    formEditUser.role_id = user.role_id;
    modalDialogEdit.value = true;
};

// update user
const updateUser = () => {
    formEditUser.put(route('users.update', formEditUser.id), {
        preserveScroll: true,
        onSuccess: (result: any) => {
            toast('Updated!', {
                description: `${result.props.flash.message}`,
                action: {
                    label: 'Close',
                    onClick: () => console.log('Closed'),
                },
            });

            modalDialogEdit.value = false;
        },
    });
};
</script>

<template>
    <Head title="Users"></Head>
    <AppLayout :breadcrumbs="breadcrumb">
        <Toaster />
        <div class="p-10">
            <Dialog>
                <DialogTrigger as-child>
                    <div class="mb-5 flex justify-end">
                        <Button @click.prevent="formCreateData.clearErrors()"> Create new User </Button>
                    </div>
                </DialogTrigger>
                <DialogContent class="sm:max-w-[500px]">
                    <DialogHeader>
                        <DialogTitle>Create User</DialogTitle>
                        <DialogDescription> Make new user here. Click Create when you're done. </DialogDescription>
                    </DialogHeader>
                    <form @submit.prevent="createUser()">
                        <div class="grid gap-4 py-4">
                            <div class="grid grid-cols-4 items-center gap-4">
                                <Label for="name" class="text-right"> Name </Label>
                                <Input id="name" v-model="formCreateData.name" class="col-span-3" />
                            </div>
                            <InputError :message="formCreateData.errors.name"></InputError>
                            <div class="grid grid-cols-4 items-center gap-4">
                                <Label for="email" class="text-right">Email </Label>
                                <Input id="email" v-model="formCreateData.email" type="email" class="col-span-3" />
                            </div>
                            <InputError :message="formCreateData.errors.email"></InputError>
                            <div class="grid grid-cols-4 items-center gap-4">
                                <Label for="role">Select role</Label>
                                <Select id="role" v-model="formCreateData.role_id">
                                    <SelectTrigger class="w-[333px]">
                                        <SelectValue placeholder="Select a role" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectItem v-for="role in roles" :key="role.id" :value="role.id">
                                                {{ role.role_name }}
                                            </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                            </div>
                            <InputError :message="formCreateData.errors.role_id" />

                            <div class="grid grid-cols-4 items-center gap-4">
                                <Label for="password" class="text-right">Password </Label>
                                <Input id="password" v-model="formCreateData.password" type="password" class="col-span-3" />
                            </div>
                            <InputError :message="formCreateData.errors.password"></InputError>

                            <div class="grid grid-cols-4 items-center gap-4">
                                <Label for="password_confirmation" class="text-left">Password Confirmation </Label>
                                <Input id="password_confirmation" v-model="formCreateData.password_confirmation" type="password" class="col-span-3" />
                            </div>
                        </div>
                        <DialogFooter>
                            <Button type="submit" :disabled="formCreateData.processing"> Create new </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
            <Table>
                <TableCaption>A list of Users</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-[100px]">No </TableHead>
                        <TableHead>Name</TableHead>
                        <TableHead>Email</TableHead>
                        <TableHead>Joined </TableHead>
                        <TableHead>Role </TableHead>
                        <TableHead>Action </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="(user, index) in users.data" :key="user.id">
                        <TableCell class="font-medium">{{ index + 1 }} </TableCell>
                        <TableCell>{{ user.name }}</TableCell>
                        <TableCell>{{ user.email }}</TableCell>
                        <TableCell>{{ user.created_at }} </TableCell>
                        <TableCell>
                            <Badge v-if="user.role_name == 'Member'" variant="secondary">{{ user.role_name ?? '-' }}</Badge>
                            <Badge v-else>{{ user.role_name ?? '-' }}</Badge>
                        </TableCell>
                        <TableCell>
                            <div class="space-x-2">
                                <Button size="sm" variant="secondary" @click="fillDataModalDialog(user)">
                                    <Edit />
                                </Button>

                                <!-- delete user -->
                                <AlertDialog>
                                    <AlertDialogTrigger as-child>
                                        <Button size="sm" variant="destructive" :disabled="formDeleteData.processing">
                                            <Trash />
                                        </Button>
                                    </AlertDialogTrigger>
                                    <AlertDialogContent>
                                        <AlertDialogHeader>
                                            <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
                                            <AlertDialogDescription> You are going to delete user "{{ user?.name }}" </AlertDialogDescription>
                                        </AlertDialogHeader>
                                        <AlertDialogFooter>
                                            <AlertDialogCancel>Cancel</AlertDialogCancel>
                                            <AlertDialogAction @click.prevent="destroyUser(user.id)" class="bg-red-600 hover:bg-red-700">
                                                Delete
                                            </AlertDialogAction>
                                        </AlertDialogFooter>
                                    </AlertDialogContent>
                                </AlertDialog>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <!-- modal dialog edit -->
            <Dialog v-model:open="modalDialogEdit">
                <DialogContent className="sm:max-w-[425px]">
                    <form @submit.prevent="updateUser()">
                        <DialogHeader>
                            <DialogTitle>Edit profile</DialogTitle>
                            <DialogDescription> Make changes to your profile here. Click save when you&apos;re done. </DialogDescription>
                        </DialogHeader>
                        <div className="grid gap-4 mt-4">
                            <div className="grid gap-3">
                                <Label htmlFor="name">Name</Label>
                                <div>
                                    <Input id="name" name="name" v-model="formEditUser.name" />
                                    <InputError :message="formEditUser.errors.name"></InputError>
                                </div>
                            </div>
                            <div className="grid gap-3">
                                <Label htmlFor="email">Email</Label>
                                <div>
                                    <Input id="email" name="email" v-model="formEditUser.email" />
                                    <InputError :message="formEditUser.errors.email"></InputError>
                                </div>
                            </div>
                            <div class="grid gap-3">
                                <Label htmlFor="role">Select role</Label>

                                <Select v-model="formEditUser.role_id">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Select a role" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectItem v-for="role in roles" :key="role.id" :value="role.id">
                                                {{ role.role_name }}
                                            </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>
                        <DialogFooter class="mt-4">
                            <DialogClose asChild>
                                <Button variant="outline">Cancel</Button>
                            </DialogClose>
                            <Button type="submit">Save changes</Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
