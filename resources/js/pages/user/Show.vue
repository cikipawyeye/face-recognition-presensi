<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatHumanDate } from '@/lib/helpers';
import { BreadcrumbItem, User } from '@/types';
import { Head } from '@inertiajs/vue3';
import Edit from './partials/Edit.vue';
import Delete from './partials/Delete.vue';

const props = defineProps<{
    user: User;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Users',
        href: '/users',
    },
    {
        title: props.user.name,
        href: '/users/create',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Detail Users" />

        <div class="px-4 py-6">
            <Heading title="Detail Users" />

            <div class="mt-7 overflow-x-auto">
                <Card class="">
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <h1 class="text-lg font-semibold">User Details</h1>
                            <div class="flex items-center gap-2">
                                <Edit :user="user" />
                                <Delete :user="user" />
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="flex-1">
                                <h2 class="font-semibold">Name</h2>
                                <p>{{ user.name }}</p>
                            </div>
                            <div class="flex-1">
                                <h2 class="font-semibold">Email</h2>
                                <p>{{ user.email }}</p>
                            </div>
                            <div class="flex-1">
                                <h2 class="font-semibold">Registered at</h2>
                                <p>{{ formatHumanDate(user.created_at, 'dd LLL yyyy, HH:mm') }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
