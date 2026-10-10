<template>
    <Head title="Buat Laporan Baru" />

    <AuthenticatedLayout>

        <template #header>
            <div>
                <h1 class="text-[22px] font-bold leading-tight text-black">
                    Buat Laporan Baru
                </h1>

                <p class="mt-1 text-[13px] text-gray-500">
                    Laporkan permasalahan lingkungan di sekitar Anda
                </p>
            </div>
        </template>

        <div
            class="min-h-full bg-[#F5F7FA] px-6 py-8 lg:px-12"
        >
            <div
                class="mx-auto max-w-[1080px] rounded-[22px] bg-white p-6 shadow-[0_8px_30px_rgba(0,0,0,0.06)] lg:p-7"
            >

                <!-- HEADER -->
                <div class="mb-7">
                    <h1
                        class="text-[26px] font-bold text-black"
                    >
                        Informasi Laporan
                    </h1>

                    <p
                        class="mt-1 text-[14px] text-[#737D8F]"
                    >
                        Lengkapi informasi permasalahan lingkungan yang Anda temukan.
                    </p>
                </div>

                <form
                    @submit.prevent="submit"
                >

                    <!-- JUDUL + KATEGORI -->
                    <div
                        class="grid gap-5 lg:grid-cols-2"
                    >

                        <!-- JUDUL -->
                        <div>
                            <label
                                class="mb-2 block text-[15px] font-medium text-black"
                            >
                                Judul Laporan
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                v-model="form.title"
                                type="text"
                                placeholder="Contoh: Tumpukan sampah di pinggir jalan"
                                class="h-[52px] w-full rounded-xl border border-[#D7DCE3] bg-white px-4 text-[14px] text-black outline-none transition placeholder:text-[#7A8496] focus:border-[#1B5E20] focus:ring-2 focus:ring-[#1B5E20]/10"
                            />

                            <p
                                v-if="form.errors.title"
                                class="mt-1 text-sm text-red-500"
                            >
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <!-- KATEGORI -->
                        <div>
                            <label
                                class="mb-2 block text-[15px] font-medium text-black"
                            >
                                Kategori Laporan
                                <span class="text-red-500">*</span>
                            </label>

                            <select
                                v-model="form.category_id"
                                class="h-[52px] w-full rounded-xl border border-[#D7DCE3] bg-white px-4 text-[14px] text-black outline-none transition focus:border-[#1B5E20] focus:ring-2 focus:ring-[#1B5E20]/10"
                            >
                                <option
                                    value=""
                                    disabled
                                >
                                    Pilih kategori laporan
                                </option>

                                <option
                                    v-for="category in props.categories"
                                    :key="category.id"
                                    :value="category.id"
                                >
                                    {{ category.name }}
                                </option>
                            </select>

                            <p
                                v-if="form.errors.category_id"
                                class="mt-1 text-sm text-red-500"
                            >
                                {{ form.errors.category_id }}
                            </p>
                        </div>

                    </div>

                    <!-- DESKRIPSI -->
                    <div class="mt-6">

                        <div
                            class="mb-2 flex items-center justify-between"
                        >
                            <label
                                class="block text-[15px] font-medium text-black"
                            >
                                Deskripsi Laporan
                                <span class="text-red-500">*</span>
                            </label>

                            <span
                                class="text-[13px] text-[#667085]"
                            >
                                {{ descriptionLength }}/500
                            </span>
                        </div>

                        <textarea
                            v-model="form.description"
                            maxlength="500"
                            rows="5"
                            placeholder="Jelaskan permasalahan yang Anda temukan..."
                            class="w-full resize-none rounded-xl border border-[#D7DCE3] bg-white px-4 py-3 text-[14px] text-black outline-none transition placeholder:text-[#7A8496] focus:border-[#1B5E20] focus:ring-2 focus:ring-[#1B5E20]/10"
                            @input="updateDescription"
                        ></textarea>

                        <p
                            v-if="form.errors.description"
                            class="mt-1 text-sm text-red-500"
                        >
                            {{ form.errors.description }}
                        </p>

                    </div>

                    <!-- FOTO + LOKASI -->
                    <div
                        class="mt-6 grid gap-6 lg:grid-cols-[1fr_1.08fr]"
                    >

                        <!-- FOTO -->
                        <div>

                            <label
                                class="mb-2 block text-[15px] font-medium text-black"
                            >
                                Upload Foto
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                ref="fileInput"
                                type="file"
                                accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                                multiple
                                class="hidden"
                                @change="handleMultiplePhotoChange"
                            />

                            <!-- UPLOAD -->
                            <button
                                type="button"
                                class="flex h-[162px] w-full flex-col items-center justify-center rounded-xl border-2 border-dashed border-[#CCD3DD] bg-[#FCFDFE] transition hover:border-[#1B5E20] hover:bg-[#F8FCF8]"
                                :class="{
                                    'border-[#1B5E20] bg-[#F8FCF8]':
                                        isDragging
                                }"
                                @click="openFilePicker"
                                @dragover.prevent="handleDragOver"
                                @dragleave.prevent="handleDragLeave"
                                @drop.prevent="handleMultipleDrop"
                            >

                                <svg
                                    width="38"
                                    height="38"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path d="M12 16V4" />
                                    <path d="m7 9 5-5 5 5" />
                                    <path d="M5 20h14" />
                                </svg>

                                <span
                                    class="mt-3 text-[15px] font-medium text-black"
                                >
                                    Drag & drop foto di sini
                                </span>

                                <span
                                    class="mt-1 text-[14px] text-[#737D8F]"
                                >
                                    atau klik untuk upload
                                </span>

                                <span
                                    class="mt-1 text-[13px] text-[#737D8F]"
                                >
                                    JPG, PNG · Maks. 5 MB
                                </span>

                            </button>

                            <!-- FOTO YANG SUDAH DIUPLOAD -->
                            <div
                                v-if="photoPreviewUrls.length"
                                class="mt-4 grid grid-cols-4 gap-3"
                            >

                                <!-- PREVIEW FOTO -->
                                <div
                                    v-for="(preview, index) in photoPreviewUrls"
                                    :key="preview"
                                    class="relative h-[110px] overflow-hidden rounded-xl border border-[#D7DCE3] bg-[#F5F7FA]"
                                >

                                    <img
                                        :src="preview"
                                        :alt="`Foto laporan ${index + 1}`"
                                        class="h-full w-full object-cover"
                                    />

                                    <button
                                        type="button"
                                        class="absolute right-1.5 top-1.5 flex h-6 w-6 items-center justify-center rounded-full bg-black/55 text-sm leading-none text-white transition hover:bg-black/75"
                                        @click="removeMultiplePhoto(index)"
                                    >
                                        ×
                                    </button>

                                </div>

                                <!-- TAMBAH FOTO -->
                                <button
                                    v-if="selectedPhotos.length < 5"
                                    type="button"
                                    class="flex h-[110px] items-center justify-center rounded-xl border border-[#D7DCE3] bg-white text-black transition hover:border-[#1B5E20] hover:bg-[#F8FCF8]"
                                    @click="openFilePicker"
                                >

                                    <svg
                                        width="30"
                                        height="30"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M12 5v14" />
                                        <path d="M5 12h14" />
                                    </svg>

                                </button>

                            </div>

                            <p
                                v-if="form.errors.photos"
                                class="mt-1 text-sm text-red-500"
                            >
                                {{ form.errors.photos }}
                            </p>

                            <p
                                v-if="form.errors['photos.0']"
                                class="mt-1 text-sm text-red-500"
                            >
                                {{ form.errors['photos.0'] }}
                            </p>

                        </div>

                        <!-- LOKASI -->
                        <div>

                            <label
                                class="mb-1 block text-[15px] font-medium text-black"
                            >
                                Lokasi Laporan
                                <span class="text-red-500">*</span>
                            </label>

                            <p
                                class="mb-3 text-[13px] text-[#7A8496]"
                            >
                                Klik pada peta untuk memilih lokasi atau gunakan GPS.
                            </p>

                            <!-- MAP -->
                            <div
                                ref="mapElement"
                                class="h-[170px] w-full overflow-hidden rounded-xl border border-[#D7DCE3]"
                            ></div>

                            <!-- GPS -->
                            <button
                                type="button"
                                :disabled="isGettingLocation"
                                class="mt-3 flex h-[42px] w-full items-center justify-center gap-2 rounded-xl border border-[#1B5E20] bg-white text-[14px] font-medium text-[#1B5E20] transition hover:bg-[#F0F8F1] disabled:cursor-not-allowed disabled:opacity-60"
                                @click="getCurrentLocation"
                            >

                                <svg
                                    width="19"
                                    height="19"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="3"
                                    />

                                    <path d="M12 2v3" />
                                    <path d="M12 19v3" />
                                    <path d="M2 12h3" />
                                    <path d="M19 12h3" />
                                </svg>

                                {{
                                    isGettingLocation
                                        ? 'Mengambil lokasi...'
                                        : 'Gunakan lokasi saat ini'
                                }}

                            </button>

                            <!-- ALAMAT -->
                            <div
                                class="relative mt-3"
                            >

                                <textarea
                                    v-model="form.address"
                                    rows="2"
                                    placeholder="Alamat lokasi laporan"
                                    class="w-full resize-none rounded-xl border border-[#D7DCE3] bg-white py-3 pl-11 pr-4 text-[13px] text-black outline-none transition placeholder:text-[#7A8496] focus:border-[#1B5E20] focus:ring-2 focus:ring-[#1B5E20]/10"
                                ></textarea>

                                <svg
                                    class="absolute left-4 top-3.5"
                                    width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="#111827"
                                    stroke-width="1.8"
                                >
                                    <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z" />

                                    <circle
                                        cx="12"
                                        cy="10"
                                        r="2.5"
                                    />
                                </svg>

                            </div>

                            <p
                                v-if="locationMessage"
                                class="mt-1 text-xs text-[#667085]"
                            >
                                {{ locationMessage }}
                            </p>

                            <p
                                v-if="
                                    form.errors.latitude ||
                                    form.errors.longitude
                                "
                                class="mt-1 text-sm text-red-500"
                            >
                                Silakan pilih lokasi pada peta atau gunakan lokasi saat ini.
                            </p>

                            <p
                                v-if="form.errors.address"
                                class="mt-1 text-sm text-red-500"
                            >
                                {{ form.errors.address }}
                            </p>

                        </div>

                    </div>

                    <!-- ACTION -->
                    <div
                        class="mt-7 flex justify-end gap-3"
                    >

                        <Link
                            :href="route('dashboard')"
                            class="flex h-[46px] min-w-[170px] items-center justify-center rounded-xl border border-[#D7DCE3] bg-white px-6 text-[14px] font-semibold text-black transition hover:bg-[#F7F8FA]"
                        >
                            Batal
                        </Link>

                        <button
                            type="submit"
                            :disabled="
                                form.processing ||
                                selectedPhotos.length === 0
                            "
                            class="flex h-[46px] min-w-[220px] items-center justify-center gap-2 rounded-xl bg-[#168333] px-6 text-[14px] font-semibold text-white transition hover:bg-[#126B2A] disabled:cursor-not-allowed disabled:opacity-60"
                        >

                            {{
                                form.processing
                                    ? 'Mengirim...'
                                    : 'Kirim Laporan'
                            }}

                            <svg
                                width="23"
                                height="23"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="m22 2-7 20-4-9-9-4Z" />
                                <path d="M22 2 11 13" />
                            </svg>

                        </button>

                    </div>

                </form>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { onMounted, onUnmounted, ref } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
})

const mapElement = ref(null)
const map = ref(null)
const marker = ref(null)

const fileInput = ref(null)
const previewUrl = ref(null)
const isDragging = ref(false)

const isGettingLocation = ref(false)
const locationMessage = ref('')

const form = useForm({
    title: '',
    category_id: '',
    description: '',
    photo: null,
    photos: [],
    latitude: '',
    longitude: '',
    address: '',
})

const descriptionLength = ref(0)

/* DESKRIPSI */

const updateDescription = () => {
    descriptionLength.value = form.description.length
}

/* FOTO */

const openFilePicker = () => {
    fileInput.value?.click()
}

const validatePhoto = (file) => {
    if (!file) {
        return false
    }

    const allowedTypes = [
        'image/jpeg',
        'image/jpg',
        'image/png',
    ]

    if (!allowedTypes.includes(file.type)) {
        alert('Foto harus berformat JPG, JPEG, atau PNG.')
        return false
    }

    if (file.size > 5 * 1024 * 1024) {
        alert('Ukuran foto maksimal 5 MB.')
        return false
    }

    return true
}

/* FOTO LAMA - TETAP DIPERTAHANKAN */

const setPhoto = (file) => {
    if (!validatePhoto(file)) {
        return
    }

    form.photo = file

    if (previewUrl.value) {
        URL.revokeObjectURL(previewUrl.value)
    }

    previewUrl.value = URL.createObjectURL(file)
}

const handlePhotoChange = (event) => {
    const file = event.target.files?.[0]

    if (file) {
        setPhoto(file)
    }
}

const handleDragOver = () => {
    isDragging.value = true
}

const handleDragLeave = () => {
    isDragging.value = false
}

const handleDrop = (event) => {
    isDragging.value = false

    const file = event.dataTransfer.files?.[0]

    if (file) {
        setPhoto(file)
    }
}

const removePhoto = () => {
    form.photo = null

    if (previewUrl.value) {
        URL.revokeObjectURL(previewUrl.value)
        previewUrl.value = null
    }

    if (fileInput.value) {
        fileInput.value.value = ''
    }
}

/*FOTO MULTIPLE */

const selectedPhotos = ref([])
const photoPreviewUrls = ref([])

const addMultiplePhotos = (files) => {
    for (const file of files) {
        if (selectedPhotos.value.length >= 5) {
            alert('Maksimal 5 foto untuk satu laporan.')
            break
        }

        if (!validatePhoto(file)) {
            continue
        }

        const duplicate = selectedPhotos.value.some(
            (selectedFile) =>
                selectedFile.name === file.name &&
                selectedFile.size === file.size &&
                selectedFile.lastModified === file.lastModified
        )

        if (duplicate) {
            continue
        }

        selectedPhotos.value.push(file)

        photoPreviewUrls.value.push(
            URL.createObjectURL(file)
        )
    }

    form.photos = [...selectedPhotos.value]
}

const handleMultiplePhotoChange = (event) => {
    const files = Array.from(
        event.target.files || []
    )

    if (!files.length) {
        return
    }

    addMultiplePhotos(files)

    if (fileInput.value) {
        fileInput.value.value = ''
    }
}

const handleMultipleDrop = (event) => {
    isDragging.value = false

    const files = Array.from(
        event.dataTransfer.files || []
    )

    if (!files.length) {
        return
    }

    addMultiplePhotos(files)
}

const removeMultiplePhoto = (index) => {
    if (photoPreviewUrls.value[index]) {
        URL.revokeObjectURL(
            photoPreviewUrls.value[index]
        )
    }

    selectedPhotos.value.splice(index, 1)
    photoPreviewUrls.value.splice(index, 1)

    form.photos = [...selectedPhotos.value]
}

/* MAP */

const createMarker = (latitude, longitude) => {
    if (!map.value) {
        return
    }

    if (marker.value) {
        marker.value.setLatLng([
            latitude,
            longitude,
        ])

        return
    }

    const icon = L.divIcon({
        className: '',
        html: `
            <div style="
                width: 34px;
                height: 34px;
                border-radius: 50% 50% 50% 0;
                background: #1B5E20;
                border: 4px solid white;
                transform: rotate(-45deg);
                box-shadow: 0 2px 8px rgba(0,0,0,0.25);
                position: relative;
            ">
                <div style="
                    width: 9px;
                    height: 9px;
                    border-radius: 50%;
                    background: white;
                    position: absolute;
                    top: 9px;
                    left: 9px;
                "></div>
            </div>
        `,
        iconSize: [34, 34],
        iconAnchor: [17, 34],
    })

    marker.value = L.marker(
        [latitude, longitude],
        {
            icon,
        }
    ).addTo(map.value)
}

const setLocation = (latitude, longitude) => {
    form.latitude = Number(latitude).toFixed(8)
    form.longitude = Number(longitude).toFixed(8)

    createMarker(
        latitude,
        longitude
    )

    map.value?.setView(
        [latitude, longitude],
        17
    )
}

/* REVERSE GEOCODING */

const getAddress = async (latitude, longitude) => {
    locationMessage.value =
        'Mencari alamat lokasi...'

    try {
        const response = await fetch(
            `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latitude}&lon=${longitude}&zoom=18&addressdetails=1`,
            {
                headers: {
                    Accept: 'application/json',
                },
            }
        )

        if (!response.ok) {
            throw new Error(
                'Gagal mendapatkan alamat.'
            )
        }

        const data = await response.json()

        if (data.display_name) {
            form.address =
                data.display_name

            locationMessage.value =
                'Alamat berhasil ditemukan.'
        } else {
            locationMessage.value =
                'Alamat tidak ditemukan. Silakan isi secara manual.'
        }
    } catch (error) {
        locationMessage.value =
            'Alamat tidak ditemukan. Silakan isi secara manual.'
    }
}

/* KLIK PETA */

const handleMapClick = async (event) => {
    const latitude = event.latlng.lat
    const longitude = event.latlng.lng

    setLocation(
        latitude,
        longitude
    )

    await getAddress(
        latitude,
        longitude
    )
}

/* GPS */

const getCurrentLocation = () => {
    if (!navigator.geolocation) {
        locationMessage.value =
            'Browser tidak mendukung GPS.'

        return
    }

    isGettingLocation.value = true

    locationMessage.value =
        'Mengambil lokasi saat ini...'

    navigator.geolocation.getCurrentPosition(
        async (position) => {
            const latitude =
                position.coords.latitude

            const longitude =
                position.coords.longitude

            setLocation(
                latitude,
                longitude
            )

            await getAddress(
                latitude,
                longitude
            )

            isGettingLocation.value = false
        },

        (error) => {
            isGettingLocation.value = false

            switch (error.code) {
                case error.PERMISSION_DENIED:
                    locationMessage.value =
                        'Izin lokasi ditolak. Izinkan akses lokasi pada browser.'
                    break

                case error.POSITION_UNAVAILABLE:
                    locationMessage.value =
                        'Lokasi tidak tersedia.'
                    break

                case error.TIMEOUT:
                    locationMessage.value =
                        'Waktu mengambil lokasi habis.'
                    break

                default:
                    locationMessage.value =
                        'Gagal mengambil lokasi.'
            }
        },

        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0,
        }
    )
}

/* SUBMIT */

const submit = () => {
    form.photos = [...selectedPhotos.value]

    form.post(
        route('masyarakat.reports.store'),
        {
            forceFormData: true,
            preserveScroll: true,
        }
    )
}

/* INITIALIZE MAP */

onMounted(() => {
    map.value = L.map(
        mapElement.value
    ).setView(
        [-6.4025, 106.7942],
        13
    )

    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            maxZoom: 19,
            attribution:
                '&copy; OpenStreetMap contributors',
        }
    ).addTo(map.value)

    map.value.on(
        'click',
        handleMapClick
    )
})

onUnmounted(() => {
    if (map.value) {
        map.value.remove()
    }

    if (previewUrl.value) {
        URL.revokeObjectURL(
            previewUrl.value
        )
    }

    photoPreviewUrls.value.forEach(
        (url) => {
            URL.revokeObjectURL(url)
        }
    )
})
</script>
