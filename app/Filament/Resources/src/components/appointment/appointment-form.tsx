"use client";

import { useState, useEffect } from "react";
import { useSearchParams } from "next/navigation";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { Badge } from "@/components/ui/badge";
import { Separator } from "@/components/ui/separator";
import { appointmentSchema, type AppointmentFormData } from "@/lib/validations";
import { useMobil, useStokMobils, createJanjiTemu } from "@/lib/hooks";
import { formatCurrency } from "@/lib/utils";
import {
    Calendar,
    Clock,
    Car,
    MapPin,
    User,
    Mail,
    Phone,
    MessageSquare,
    CheckCircle,
    AlertCircle,
} from "lucide-react";
import { toast } from "sonner";

export default function AppointmentForm() {
    const searchParams = useSearchParams();
    const mobilId = searchParams.get("mobil");
    const varianId = searchParams.get("varian");

    const [isSubmitting, setIsSubmitting] = useState(false);
    const [isSuccess, setIsSuccess] = useState(false);

    const { data: mobil } = useMobil(mobilId || "");
    const { data: stokMobils } = useStokMobils(mobilId || "");

    const {
        register,
        handleSubmit,
        formState: { errors },
        setValue,
        watch,
        reset,
    } = useForm<AppointmentFormData>({
        resolver: zodResolver(appointmentSchema),
        defaultValues: {
            jenis: "konsultasi",
            metode: "offline",
            lokasi: "showroom",
        },
    });

    const selectedJenis = watch("jenis");
    const selectedMetode = watch("metode");
    useEffect(() => {
        if (
            varianId &&
            (stokMobils as any) &&
            Array.isArray(stokMobils as any)
        ) {
            const selectedVariant = (stokMobils as any[]).find(
                (stok: any) => stok.id.toString() === varianId
            );
            if (selectedVariant) {
                setValue("stok_mobil_id", selectedVariant.id);
            }
        }
    }, [varianId, stokMobils, setValue]);
    const selectedVariant =
        (stokMobils as any) && Array.isArray(stokMobils as any)
            ? (stokMobils as any[]).find(
                  (stok: any) =>
                      stok.id.toString() === watch("stok_mobil_id")?.toString()
              )
            : null;

    const onSubmit = async (data: AppointmentFormData) => {
        setIsSubmitting(true);
        try {
            await createJanjiTemu(data);
            setIsSuccess(true);
            toast.success(
                "Janji temu berhasil dibuat! Kami akan menghubungi Anda segera."
            );
            reset();
        } catch (error) {
            console.error("Error creating appointment:", error);
            toast.error("Gagal membuat janji temu. Silakan coba lagi.");
        } finally {
            setIsSubmitting(false);
        }
    };

    if (isSuccess) {
        return (
            <Card>
                <CardContent className="p-8 text-center">
                    <div className="text-green-500 mb-4">
                        <CheckCircle className="w-16 h-16 mx-auto" />
                    </div>
                    <h2 className="text-2xl font-bold text-gray-900 mb-4">
                        Janji Temu Berhasil Dibuat!
                    </h2>
                    <p className="text-gray-600 mb-6">
                        Terima kasih telah membuat janji temu dengan kami. Tim
                        kami akan menghubungi Anda dalam waktu 1x24 jam untuk
                        konfirmasi jadwal.
                    </p>
                    <div className="space-y-2 text-sm text-gray-500">
                        <p>📧 Cek email Anda untuk detail janji temu</p>
                        <p>📱 Pastikan nomor WhatsApp aktif untuk konfirmasi</p>
                        <p>🚗 Siapkan dokumen yang diperlukan</p>
                    </div>
                    <Button
                        className="mt-6"
                        onClick={() => {
                            setIsSuccess(false);
                            reset();
                        }}
                    >
                        Buat Janji Temu Lagi
                    </Button>
                </CardContent>
            </Card>
        );
    }

    return (
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {/* Form */}
            <div className="lg:col-span-2">
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <Calendar className="w-5 h-5" />
                            Formulir Janji Temu
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form
                            onSubmit={handleSubmit(onSubmit)}
                            className="space-y-6"
                        >
                            {/* Personal Information */}
                            <div>
                                <h3 className="text-lg font-semibold mb-4 flex items-center gap-2">
                                    <User className="w-5 h-5" />
                                    Informasi Pribadi
                                </h3>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <Label htmlFor="nama_pelanggan">
                                            Nama Lengkap *
                                        </Label>
                                        <Input
                                            id="nama_pelanggan"
                                            {...register("nama_pelanggan")}
                                            placeholder="Masukkan nama lengkap"
                                        />
                                        {errors.nama_pelanggan && (
                                            <p className="text-sm text-red-500 mt-1">
                                                {errors.nama_pelanggan.message}
                                            </p>
                                        )}
                                    </div>

                                    <div>
                                        <Label htmlFor="email_pelanggan">
                                            Email *
                                        </Label>
                                        <Input
                                            id="email_pelanggan"
                                            type="email"
                                            {...register("email_pelanggan")}
                                            placeholder="nama@email.com"
                                        />
                                        {errors.email_pelanggan && (
                                            <p className="text-sm text-red-500 mt-1">
                                                {errors.email_pelanggan.message}
                                            </p>
                                        )}
                                    </div>

                                    <div>
                                        <Label htmlFor="telepon_pelanggan">
                                            Nomor WhatsApp *
                                        </Label>
                                        <Input
                                            id="telepon_pelanggan"
                                            {...register("telepon_pelanggan")}
                                            placeholder="08xxxxxxxxxx"
                                        />
                                        {errors.telepon_pelanggan && (
                                            <p className="text-sm text-red-500 mt-1">
                                                {
                                                    errors.telepon_pelanggan
                                                        .message
                                                }
                                            </p>
                                        )}
                                    </div>

                                    <div>
                                        <Label htmlFor="alamat_pelanggan">
                                            Alamat
                                        </Label>
                                        <Input
                                            id="alamat_pelanggan"
                                            {...register("alamat_pelanggan")}
                                            placeholder="Alamat lengkap (opsional)"
                                        />
                                    </div>
                                </div>
                            </div>

                            <Separator />

                            {/* Car Selection */}
                            <div>
                                <h3 className="text-lg font-semibold mb-4 flex items-center gap-2">
                                    <Car className="w-5 h-5" />
                                    Pilih Mobil
                                </h3>{" "}
                                {mobilId && mobil ? (
                                    <div className="bg-gray-50 p-4 rounded-lg mb-4">
                                        <h4 className="font-medium">
                                            {(mobil as any).nama_mobil}
                                        </h4>
                                        <p className="text-sm text-gray-600">
                                            {(mobil as any).merek?.nama_merek} •{" "}
                                            {(mobil as any).tahun_produksi}
                                        </p>
                                    </div>
                                ) : (
                                    <div className="bg-yellow-50 border border-yellow-200 p-4 rounded-lg mb-4">
                                        <div className="flex items-center gap-2 text-yellow-800">
                                            <AlertCircle className="w-4 h-4" />{" "}
                                            <span className="text-sm">
                                                Pilih mobil dari katalog untuk
                                                melanjutkan
                                            </span>
                                        </div>
                                    </div>
                                )}{" "}
                                {(stokMobils as any) &&
                                    Array.isArray(stokMobils as any) && (
                                        <div>
                                            <Label htmlFor="stok_mobil_id">
                                                Pilih Varian *
                                            </Label>
                                            <Select
                                                value={
                                                    watch(
                                                        "stok_mobil_id"
                                                    )?.toString() || ""
                                                }
                                                onValueChange={(value) =>
                                                    setValue(
                                                        "stok_mobil_id",
                                                        Number(value)
                                                    )
                                                }
                                            >
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Pilih varian mobil" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {(stokMobils as any[]).map(
                                                        (stok: any) => (
                                                            <SelectItem
                                                                key={stok.id}
                                                                value={stok.id.toString()}
                                                            >
                                                                {stok.varian} -{" "}
                                                                {formatCurrency(
                                                                    stok.harga
                                                                )}{" "}
                                                                ({stok.warna})
                                                            </SelectItem>
                                                        )
                                                    )}
                                                </SelectContent>
                                            </Select>
                                            {errors.stok_mobil_id && (
                                                <p className="text-sm text-red-500 mt-1">
                                                    {
                                                        errors.stok_mobil_id
                                                            .message
                                                    }
                                                </p>
                                            )}
                                        </div>
                                    )}
                            </div>

                            <Separator />

                            {/* Appointment Details */}
                            <div>
                                <h3 className="text-lg font-semibold mb-4 flex items-center gap-2">
                                    <Clock className="w-5 h-5" />
                                    Detail Janji Temu
                                </h3>

                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <Label htmlFor="jenis">
                                            Jenis Layanan *
                                        </Label>
                                        <Select
                                            value={selectedJenis}
                                            onValueChange={(value) =>
                                                setValue("jenis", value as any)
                                            }
                                        >
                                            <SelectTrigger>
                                                <SelectValue />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="test_drive">
                                                    Test Drive
                                                </SelectItem>
                                                <SelectItem value="konsultasi">
                                                    Konsultasi
                                                </SelectItem>
                                                <SelectItem value="negosiasi">
                                                    Negosiasi Harga
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>

                                    <div>
                                        <Label htmlFor="metode">
                                            Metode Pertemuan *
                                        </Label>
                                        <Select
                                            value={selectedMetode}
                                            onValueChange={(value) =>
                                                setValue("metode", value as any)
                                            }
                                        >
                                            <SelectTrigger>
                                                <SelectValue />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="offline">
                                                    Tatap Muka
                                                </SelectItem>
                                                <SelectItem value="online">
                                                    Video Call
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                </div>

                                {selectedMetode === "offline" && (
                                    <div className="mb-4">
                                        <Label htmlFor="lokasi">
                                            Lokasi Pertemuan *
                                        </Label>
                                        <Select
                                            value={watch("lokasi")}
                                            onValueChange={(value) =>
                                                setValue("lokasi", value as any)
                                            }
                                        >
                                            <SelectTrigger>
                                                <SelectValue />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="showroom">
                                                    Showroom Kami
                                                </SelectItem>
                                                <SelectItem value="rumah_pelanggan">
                                                    Kunjungan ke Rumah
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                )}

                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <Label htmlFor="waktu_mulai">
                                            Tanggal & Waktu Mulai *
                                        </Label>
                                        <Input
                                            id="waktu_mulai"
                                            type="datetime-local"
                                            {...register("waktu_mulai")}
                                            min={new Date()
                                                .toISOString()
                                                .slice(0, 16)}
                                        />
                                        {errors.waktu_mulai && (
                                            <p className="text-sm text-red-500 mt-1">
                                                {errors.waktu_mulai.message}
                                            </p>
                                        )}
                                    </div>

                                    <div>
                                        <Label htmlFor="waktu_selesai">
                                            Waktu Selesai *
                                        </Label>
                                        <Input
                                            id="waktu_selesai"
                                            type="datetime-local"
                                            {...register("waktu_selesai")}
                                            min={
                                                watch("waktu_mulai") ||
                                                new Date()
                                                    .toISOString()
                                                    .slice(0, 16)
                                            }
                                        />
                                        {errors.waktu_selesai && (
                                            <p className="text-sm text-red-500 mt-1">
                                                {errors.waktu_selesai.message}
                                            </p>
                                        )}
                                    </div>
                                </div>

                                <div className="mb-4">
                                    <Label htmlFor="waktu_alternatif">
                                        Waktu Alternatif
                                    </Label>
                                    <Input
                                        id="waktu_alternatif"
                                        type="datetime-local"
                                        {...register("waktu_alternatif")}
                                        min={new Date()
                                            .toISOString()
                                            .slice(0, 16)}
                                    />
                                    <p className="text-xs text-gray-500 mt-1">
                                        Opsional: Waktu alternatif jika jadwal
                                        utama tidak tersedia
                                    </p>
                                </div>

                                <div className="mb-4">
                                    <Label htmlFor="tujuan">
                                        Tujuan Khusus
                                    </Label>
                                    <Input
                                        id="tujuan"
                                        {...register("tujuan")}
                                        placeholder="Misal: Ingin test drive untuk kebutuhan keluarga"
                                    />
                                </div>

                                <div>
                                    <Label htmlFor="pesan_tambahan">
                                        Pesan Tambahan
                                    </Label>
                                    <Textarea
                                        id="pesan_tambahan"
                                        {...register("pesan_tambahan")}
                                        placeholder="Informasi tambahan yang ingin Anda sampaikan..."
                                        rows={4}
                                    />
                                </div>
                            </div>

                            <Separator />

                            {/* Submit Button */}
                            <Button
                                type="submit"
                                className="w-full"
                                size="lg"
                                disabled={isSubmitting}
                            >
                                {isSubmitting
                                    ? "Memproses..."
                                    : "Buat Janji Temu"}
                            </Button>
                        </form>
                    </CardContent>
                </Card>
            </div>

            {/* Sidebar */}
            <div className="space-y-6">
                {/* Selected Car Info */}
                {selectedVariant && (
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-lg">
                                Mobil Dipilih
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-3">
                                {" "}
                                <div>
                                    <h4 className="font-semibold">
                                        {(mobil as any)?.nama_mobil}
                                    </h4>
                                    <p className="text-sm text-gray-600">
                                        {(mobil as any)?.merek?.nama_merek} •{" "}
                                        {(mobil as any)?.tahun_produksi}
                                    </p>
                                </div>
                                <div className="bg-gray-50 p-3 rounded-lg">
                                    <p className="font-medium">
                                        {selectedVariant.varian}
                                    </p>
                                    <p className="text-xl font-bold text-primary">
                                        {formatCurrency(selectedVariant.harga)}
                                    </p>
                                    <div className="flex gap-2 mt-2">
                                        <Badge variant="secondary">
                                            {selectedVariant.warna}
                                        </Badge>
                                        <Badge variant="secondary">
                                            {selectedVariant.transmisi}
                                        </Badge>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                )}

                {/* Contact Info */}
                <Card>
                    <CardHeader>
                        <CardTitle className="text-lg flex items-center gap-2">
                            <Phone className="w-5 h-5" />
                            Informasi Kontak
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="flex items-center gap-3">
                            <Phone className="w-5 h-5 text-primary" />
                            <div>
                                <p className="font-medium">+62 812-3456-7890</p>
                                <p className="text-sm text-gray-600">
                                    WhatsApp & Telepon
                                </p>
                            </div>
                        </div>

                        <div className="flex items-center gap-3">
                            <Mail className="w-5 h-5 text-primary" />
                            <div>
                                <p className="font-medium">
                                    info@tokojayamotor.com
                                </p>
                                <p className="text-sm text-gray-600">Email</p>
                            </div>
                        </div>

                        <div className="flex items-start gap-3">
                            <MapPin className="w-5 h-5 text-primary mt-1" />
                            <div>
                                <p className="font-medium">Toko Jaya Motor</p>
                                <p className="text-sm text-gray-600">
                                    Jl. Raya Utama No. 123
                                    <br />
                                    Jakarta Selatan, DKI Jakarta
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Business Hours */}
                <Card>
                    <CardHeader>
                        <CardTitle className="text-lg flex items-center gap-2">
                            <Clock className="w-5 h-5" />
                            Jam Operasional
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="space-y-2 text-sm">
                            <div className="flex justify-between">
                                <span>Senin - Jumat</span>
                                <span className="font-medium">
                                    08:00 - 17:00
                                </span>
                            </div>
                            <div className="flex justify-between">
                                <span>Sabtu</span>
                                <span className="font-medium">
                                    08:00 - 15:00
                                </span>
                            </div>
                            <div className="flex justify-between">
                                <span>Minggu</span>
                                <span className="text-gray-500">Tutup</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Guidelines */}
                <Card>
                    <CardHeader>
                        <CardTitle className="text-lg flex items-center gap-2">
                            <MessageSquare className="w-5 h-5" />
                            Catatan Penting
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="space-y-2 text-sm text-gray-600">
                            <p>• Konfirmasi akan dikirim via WhatsApp</p>
                            <p>• Bawa KTP untuk test drive</p>
                            <p>• Test drive maksimal 30 menit</p>
                            <p>• Reschedule H-1 jika berhalangan</p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    );
}
