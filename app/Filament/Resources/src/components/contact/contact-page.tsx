"use client";

import { useState } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Separator } from "@/components/ui/separator";
import { Badge } from "@/components/ui/badge";
import {
    MapPin,
    Phone,
    Mail,
    Clock,
    MessageCircle,
    Send,
    CheckCircle,
    Instagram,
    Facebook,
    Twitter,
} from "lucide-react";
import { toast } from "sonner";

const contactSchema = z.object({
    nama: z.string().min(2, "Nama minimal 2 karakter"),
    email: z.string().email("Email tidak valid"),
    telepon: z.string().min(10, "Nomor telepon minimal 10 digit"),
    subjek: z.string().min(5, "Subjek minimal 5 karakter"),
    pesan: z.string().min(10, "Pesan minimal 10 karakter"),
});

type ContactFormData = z.infer<typeof contactSchema>;

export default function ContactPage() {
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [isSuccess, setIsSuccess] = useState(false);

    const {
        register,
        handleSubmit,
        formState: { errors },
        reset,
    } = useForm<ContactFormData>({
        resolver: zodResolver(contactSchema),
    });

    const onSubmit = async (data: ContactFormData) => {
        setIsSubmitting(true);
        try {
            // Simulate API call
            await new Promise((resolve) => setTimeout(resolve, 2000));
            console.log("Contact form data:", data);
            setIsSuccess(true);
            toast.success(
                "Pesan berhasil dikirim! Kami akan menghubungi Anda segera."
            );
            reset();
        } catch (error) {
            console.error("Error sending message:", error);
            toast.error("Gagal mengirim pesan. Silakan coba lagi.");
        } finally {
            setIsSubmitting(false);
        }
    };

    const handleWhatsApp = () => {
        const message = encodeURIComponent(
            "Halo, saya ingin bertanya tentang mobil bekas di Toko Jaya Motor."
        );
        window.open(`https://wa.me/628123456789?text=${message}`, "_blank");
    };

    const handleCall = () => {
        window.open("tel:+628123456789", "_self");
    };

    if (isSuccess) {
        return (
            <div className="min-h-screen bg-gray-50 py-8">
                <div className="container mx-auto px-4">
                    <div className="max-w-2xl mx-auto">
                        <Card>
                            <CardContent className="p-8 text-center">
                                <div className="text-green-500 mb-4">
                                    <CheckCircle className="w-16 h-16 mx-auto" />
                                </div>
                                <h2 className="text-2xl font-bold text-gray-900 mb-4">
                                    Pesan Berhasil Dikirim!
                                </h2>
                                <p className="text-gray-600 mb-6">
                                    Terima kasih telah menghubungi kami. Tim
                                    customer service kami akan merespons pesan
                                    Anda dalam waktu 1x24 jam.
                                </p>
                                <Button onClick={() => setIsSuccess(false)}>
                                    Kirim Pesan Lagi
                                </Button>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-gray-50 py-8">
            <div className="container mx-auto px-4">
                <div className="max-w-6xl mx-auto">
                    {/* Header */}
                    <div className="text-center mb-8">
                        <h1 className="text-3xl font-bold text-gray-900 mb-4">
                            Kontak Kami
                        </h1>
                        <p className="text-gray-600 max-w-2xl mx-auto">
                            Butuh bantuan atau informasi lebih lanjut? Jangan
                            ragu untuk menghubungi kami. Tim kami siap membantu
                            Anda menemukan mobil bekas yang tepat.
                        </p>
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        {/* Contact Form */}
                        <div className="lg:col-span-2">
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <MessageCircle className="w-5 h-5" />
                                        Kirim Pesan
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <form
                                        onSubmit={handleSubmit(onSubmit)}
                                        className="space-y-6"
                                    >
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <Label htmlFor="nama">
                                                    Nama Lengkap *
                                                </Label>
                                                <Input
                                                    id="nama"
                                                    {...register("nama")}
                                                    placeholder="Masukkan nama lengkap"
                                                />
                                                {errors.nama && (
                                                    <p className="text-sm text-red-500 mt-1">
                                                        {errors.nama.message}
                                                    </p>
                                                )}
                                            </div>

                                            <div>
                                                <Label htmlFor="email">
                                                    Email *
                                                </Label>
                                                <Input
                                                    id="email"
                                                    type="email"
                                                    {...register("email")}
                                                    placeholder="nama@email.com"
                                                />
                                                {errors.email && (
                                                    <p className="text-sm text-red-500 mt-1">
                                                        {errors.email.message}
                                                    </p>
                                                )}
                                            </div>
                                        </div>

                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <Label htmlFor="telepon">
                                                    Nomor Telepon *
                                                </Label>
                                                <Input
                                                    id="telepon"
                                                    {...register("telepon")}
                                                    placeholder="08xxxxxxxxxx"
                                                />
                                                {errors.telepon && (
                                                    <p className="text-sm text-red-500 mt-1">
                                                        {errors.telepon.message}
                                                    </p>
                                                )}
                                            </div>

                                            <div>
                                                <Label htmlFor="subjek">
                                                    Subjek *
                                                </Label>
                                                <Input
                                                    id="subjek"
                                                    {...register("subjek")}
                                                    placeholder="Misal: Pertanyaan tentang Toyota Avanza"
                                                />
                                                {errors.subjek && (
                                                    <p className="text-sm text-red-500 mt-1">
                                                        {errors.subjek.message}
                                                    </p>
                                                )}
                                            </div>
                                        </div>

                                        <div>
                                            <Label htmlFor="pesan">
                                                Pesan *
                                            </Label>
                                            <Textarea
                                                id="pesan"
                                                {...register("pesan")}
                                                placeholder="Tuliskan pesan atau pertanyaan Anda di sini..."
                                                rows={6}
                                            />
                                            {errors.pesan && (
                                                <p className="text-sm text-red-500 mt-1">
                                                    {errors.pesan.message}
                                                </p>
                                            )}
                                        </div>

                                        <Button
                                            type="submit"
                                            className="w-full"
                                            size="lg"
                                            disabled={isSubmitting}
                                        >
                                            <Send className="w-4 h-4 mr-2" />
                                            {isSubmitting
                                                ? "Mengirim..."
                                                : "Kirim Pesan"}
                                        </Button>
                                    </form>
                                </CardContent>
                            </Card>
                        </div>

                        {/* Contact Information */}
                        <div className="space-y-6">
                            {/* Quick Contact */}
                            <Card>
                                <CardHeader>
                                    <CardTitle>Hubungi Langsung</CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <Button
                                        onClick={handleWhatsApp}
                                        className="w-full bg-green-600 hover:bg-green-700"
                                        size="lg"
                                    >
                                        <MessageCircle className="w-4 h-4 mr-2" />
                                        WhatsApp
                                    </Button>

                                    <Button
                                        onClick={handleCall}
                                        variant="outline"
                                        className="w-full"
                                        size="lg"
                                    >
                                        <Phone className="w-4 h-4 mr-2" />
                                        Telepon
                                    </Button>
                                </CardContent>
                            </Card>

                            {/* Contact Details */}
                            <Card>
                                <CardHeader>
                                    <CardTitle>Informasi Kontak</CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="flex items-start gap-3">
                                        <MapPin className="w-5 h-5 text-primary mt-1" />
                                        <div>
                                            <h4 className="font-medium">
                                                Alamat Showroom
                                            </h4>
                                            <p className="text-sm text-gray-600">
                                                Jl. Raya Utama No. 123
                                                <br />
                                                Jakarta Selatan, DKI Jakarta
                                                12345
                                            </p>
                                        </div>
                                    </div>

                                    <Separator />

                                    <div className="flex items-center gap-3">
                                        <Phone className="w-5 h-5 text-primary" />
                                        <div>
                                            <h4 className="font-medium">
                                                Telepon
                                            </h4>
                                            <p className="text-sm text-gray-600">
                                                +62 812-3456-7890
                                            </p>
                                        </div>
                                    </div>

                                    <Separator />

                                    <div className="flex items-center gap-3">
                                        <Mail className="w-5 h-5 text-primary" />
                                        <div>
                                            <h4 className="font-medium">
                                                Email
                                            </h4>
                                            <p className="text-sm text-gray-600">
                                                info@tokojayamotor.com
                                            </p>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Business Hours */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Clock className="w-5 h-5" />
                                        Jam Operasional
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-3">
                                        <div className="flex justify-between items-center">
                                            <span className="text-sm">
                                                Senin - Jumat
                                            </span>
                                            <Badge variant="outline">
                                                08:00 - 17:00
                                            </Badge>
                                        </div>
                                        <div className="flex justify-between items-center">
                                            <span className="text-sm">
                                                Sabtu
                                            </span>
                                            <Badge variant="outline">
                                                08:00 - 15:00
                                            </Badge>
                                        </div>
                                        <div className="flex justify-between items-center">
                                            <span className="text-sm">
                                                Minggu
                                            </span>
                                            <Badge variant="secondary">
                                                Tutup
                                            </Badge>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Social Media */}
                            <Card>
                                <CardHeader>
                                    <CardTitle>Ikuti Kami</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="flex gap-3">
                                        <Button variant="outline" size="icon">
                                            <Facebook className="w-4 h-4" />
                                        </Button>
                                        <Button variant="outline" size="icon">
                                            <Instagram className="w-4 h-4" />
                                        </Button>
                                        <Button variant="outline" size="icon">
                                            <Twitter className="w-4 h-4" />
                                        </Button>
                                    </div>
                                </CardContent>
                            </Card>

                            {/* FAQ Link */}
                            <Card>
                                <CardContent className="p-4">
                                    <h4 className="font-medium mb-2">
                                        Pertanyaan Umum
                                    </h4>
                                    <p className="text-sm text-gray-600 mb-3">
                                        Cek FAQ kami untuk jawaban cepat atas
                                        pertanyaan yang sering diajukan.
                                    </p>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        className="w-full"
                                    >
                                        Lihat FAQ
                                    </Button>
                                </CardContent>
                            </Card>
                        </div>
                    </div>

                    {/* Map Section */}
                    <Card className="mt-8">
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <MapPin className="w-5 h-5" />
                                Lokasi Kami
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="bg-gray-100 h-64 rounded-lg flex items-center justify-center">
                                <div className="text-center text-gray-500">
                                    <MapPin className="w-12 h-12 mx-auto mb-2" />
                                    <p>Peta Lokasi</p>
                                    <p className="text-sm">
                                        Integrasi Google Maps akan tersedia
                                        segera
                                    </p>
                                </div>
                            </div>
                            <div className="mt-4 flex justify-center">
                                <Button variant="outline">
                                    <MapPin className="w-4 h-4 mr-2" />
                                    Buka di Google Maps
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    );
}
