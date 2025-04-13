<script lang="ts" setup>
import Loader from '@/components/Loader.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';
import { toast } from 'vue-sonner';

const video = ref<HTMLVideoElement | null>(null);
const canvas = ref<HTMLCanvasElement | null>(null);
const photo = ref<string | null>(null);
const loadingCamera = ref<boolean>(true);

let stream: MediaStream | null = null;

function takePhoto(): void {
    if (!canvas.value || !video.value) return;

    const context = canvas.value.getContext('2d');
    if (!context) return;

    context.drawImage(video.value, 0, 0, canvas.value.width, canvas.value.height);
    photo.value = canvas.value.toDataURL('image/png');
    stopCamera();
}

async function retakePhoto() {
    photo.value = null;
    await initCamera();
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Presence',
        href: '/attendances/create',
    },
];

async function initCamera() {
    loadingCamera.value = true;

    try {
        stream = await navigator.mediaDevices.getUserMedia({ video: true });

        if (video.value) {
            video.value.srcObject = stream;
            video.value
                .play()
                .then(() => {
                    loadingCamera.value = false;
                })
                .finally(() => {
                    loadingCamera.value = false;
                });
        }
    } catch (err) {
        console.error('Gagal mengakses kamera:', err);
        loadingCamera.value = false;
    }
}

function stopCamera(): void {
    if (stream) {
        stream.getTracks().forEach((track) => {
            track.stop();
        });
    }
}

function base64ToFile(dataUrl: string, filename: string): File {
    const arr = dataUrl.split(',');
    const mimeMatch = arr[0].match(/:(.*?);/);
    const mime = mimeMatch ? mimeMatch[1] : '';
    const bstr = atob(arr[1]);
    let n = bstr.length;
    const u8arr = new Uint8Array(n);

    while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
    }

    return new File([u8arr], filename, { type: mime });
}

const form = useForm<{ photo: File | null }>({
    photo: null,
});

const submit = () => {
    if (!photo.value) return;

    const file = base64ToFile(photo.value, 'attendance.png');
    form.photo = file;

    form.post(route('attendances.store'), {
        forceFormData: true,
        onSuccess: () => {
            toast.success('Attendance has been recorded.', {
                description: new Date().toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: 'numeric',
                    minute: 'numeric',
                }),
                richColors: true,
            });
        },
        onError: () => {
            toast.warning(form.errors.photo ?? 'Failed to make attendance', {
                richColors: true,
            });
        },
    });
};

onMounted(async () => {
    await initCamera();
});

onUnmounted(() => {
    stopCamera();
});
</script>

<template>
    <Head title="Attendance" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <canvas ref="canvas" width="300" height="225" style="display: none"></canvas>

        <div v-if="loadingCamera" class="m-auto flex aspect-video w-full px-2 py-4 md:w-[80%] xl:w-[70%]">
            <Loader class="m-auto" />
        </div>

        <div :class="{ 'opacity-0': loadingCamera }" class="flex px-2 py-4">
            <video v-if="!photo" class="m-auto aspect-video w-full md:w-[80%] xl:w-[70%]" ref="video" autoplay playsinline></video>
            <img v-else :src="photo" class="m-auto aspect-video w-full object-contain md:w-[80%] xl:w-[70%]" alt="Hasil Gambar" />
        </div>

        <br />

        <div v-if="!loadingCamera" class="flex px-4 py-4">
            <Button v-if="!photo" class="m-auto" @click="takePhoto">Ambil Gambar</Button>

            <div v-else class="flex w-full flex-col gap-4">
                <Button :disabled="form.processing" variant="secondary" class="m-auto" @click="retakePhoto">Ambil Ulang</Button>
                <Button :disabled="form.processing" class="m-auto" @click="submit">Submit</Button>
            </div>
        </div>

        <br />
    </AppLayout>
</template>
