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
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { Edit, Trash } from 'lucide-vue-next';
import { ref } from 'vue';
import { Toaster, toast } from 'vue-sonner';
import 'vue-sonner/style.css';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Roles',
        href: '/roles',
    },
];
type RolesType = {
    id: number;
    role_name: string;
    slug: string;
    users_count?: number;
    created_at?: string;
};
defineProps<{
    roles: {
        data: RolesType[];
    };
}>();

const formCreateRole = useForm({
    role_name: '',
});

function createRole() {
    formCreateRole.post(route('roles.store'), {
        preserveScroll: true,
        onSuccess: (result: any) => {
            formCreateRole.reset();
            toast('Success', {
                description: `${result.props.flash.message}`,
                action: {
                    label: 'Close',
                },
            });
        },
    });
}

const formDeleteUser = useForm({
    id: NaN,
});

function destroyRole(id: number) {
    formDeleteUser.id = id;

    formDeleteUser.delete(route('roles.destroy', id), {
        preserveScroll: true,
        onSuccess: (result: any) => {
            toast('Deleted', {
                description: `${result.props.flash.message}`,
                action: {
                    label: 'Close',
                },
            });
        },
    });
}

const openModalEdit = ref(false);

const formEditRole = useForm({
    id: NaN,
    role_name: '',
});

function fillDataModal(role: RolesType) {
    formEditRole.id = role.id;
    formEditRole.role_name = role.role_name;
    openModalEdit.value = true;
}

function updateRole() {
    formEditRole.put(route('roles.update', formEditRole.id), {
        preserveScroll: true,
        onSuccess: (result: any) => {
            toast('Updated', {
                description: `${result.props.flash.message}`,
                action: {
                    label: 'Close',
                },
            });

            openModalEdit.value = false;
        },
    });
}
</script>

<template>
    <Head title="Roles"></Head>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Toaster></Toaster>

        <div class="p-10">
            <div class="mb-2">
                <Dialog>
                    <DialogTrigger asChild>
                        <div class="flex justify-end">
                            <Button @click="formCreateRole.resetAndClearErrors()">Create new role</Button>
                        </div>
                    </DialogTrigger>
                    <DialogContent className="sm:max-w-[425px]">
                        <form @submit.prevent="createRole()">
                            <DialogHeader>
                                <DialogTitle>Create Role</DialogTitle>
                                <DialogDescription> Create a new role for your users. </DialogDescription>
                            </DialogHeader>
                            <div className="grid gap-4 mt-3">
                                <div className="grid gap-3">
                                    <Label htmlFor="role_name">Role Name</Label>
                                    <div>
                                        <Input id="role_name" v-model="formCreateRole.role_name" name="role_name" />
                                        <InputError class="mt-1" :message="formCreateRole.errors.role_name"></InputError>
                                    </div>
                                </div>
                            </div>
                            <DialogFooter class="mt-2">
                                <Button type="submit" :disabled="formCreateRole.processing">Create new</Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>
            <Table>
                <TableCaption>A list of your recent roles.</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead class="text-left">No</TableHead>
                        <TableHead className="font-medium text-left">Role Name</TableHead>
                        <TableHead>User Count</TableHead>
                        <TableHead>Created at</TableHead>
                        <TableHead>Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="(role, index) in roles.data" :key="role.id">
                        <TableCell>{{ index + 1 }}</TableCell>
                        <TableCell className="font-medium">{{ role.role_name }}</TableCell>
                        <TableCell>{{ role.users_count }}</TableCell>
                        <TableCell>{{ role.created_at }}</TableCell>
                        <TableCell>
                            <div class="flex space-x-2">
                                <!-- button edit role -->
                                <Button variant="secondary" size="sm" @click="fillDataModal(role)">
                                    <Edit />
                                </Button>
                                <!-- delete user -->
                                <AlertDialog>
                                    <AlertDialogTrigger as-child>
                                        <Button size="sm" variant="destructive" :disabled="formDeleteUser.processing">
                                            <Trash />
                                        </Button>
                                    </AlertDialogTrigger>
                                    <AlertDialogContent>
                                        <AlertDialogHeader>
                                            <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
                                            <AlertDialogDescription> You are going to delete user "{{ role?.role_name }}" </AlertDialogDescription>
                                        </AlertDialogHeader>
                                        <AlertDialogFooter>
                                            <AlertDialogCancel>Cancel</AlertDialogCancel>
                                            <AlertDialogAction @click.prevent="destroyRole(role.id)" class="bg-red-600 hover:bg-red-700">
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

            <!-- modal dialog edit role -->
            <Dialog v-model:open="openModalEdit">
                <DialogContent className="sm:max-w-[400px]">
                    <form @submit.prevent="updateRole()">
                        <DialogHeader>
                            <DialogTitle>Update Role</DialogTitle>
                            <DialogDescription> Update role for your users. </DialogDescription>
                        </DialogHeader>
                        <div className="grid gap-4 mt-3">
                            <div className="grid gap-3">
                                <Label htmlFor="role_name">Role Name</Label>
                                <div>
                                    <Input id="role_name" v-model="formEditRole.role_name" name="role_name" />
                                    <InputError class="mt-1" :message="formEditRole.errors.role_name"></InputError>
                                </div>
                            </div>
                        </div>
                        <DialogFooter class="mt-2">
                            <Button type="submit" :disabled="formEditRole.processing">Save</Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
