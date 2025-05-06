<script setup lang="ts">
import Loader from '@/components/Loader.vue';
import PaginationComponent from '@/components/Pagination.vue';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatHumanDate } from '@/lib/helpers';
import { Attendance, BreadcrumbItem, Pagination } from '@/types';
import { Deferred, Head } from '@inertiajs/vue3';

defineProps<{
    attendances?: Pagination<Attendance>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'My Attendance',
        href: '/attendances',
    },
];
</script>

<template>
    <Head title="Presensi Saya" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <Deferred :data="['attendances']">
                <template #fallback>
                    <Loader />
                </template>

                <Table v-if="attendances != null">
                    <TableCaption>
                        <PaginationComponent :data="attendances" />
                    </TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead> Date </TableHead>
                            <TableHead> Check in </TableHead>
                            <TableHead> Check out </TableHead>
                            <TableHead> Status </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="(item, index) in attendances.data" :key="index">
                            <TableCell>{{ item.time }}</TableCell>
                            <TableCell>{{ item?.check_in ? formatHumanDate(item?.check_in) : '-' }}</TableCell>
                            <TableCell>{{ item?.check_out ? formatHumanDate(item?.check_out) : '-' }}</TableCell>
                            <TableCell> {{ item.status }} </TableCell>
                        </TableRow>
                        <TableRow v-if="attendances.data.length < 1">
                            <TableCell colspan="10" class="py-8 text-center"> No data found. </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </Deferred>
        </div>
    </AppLayout>
</template>
