"use client";

import { useState } from "react";
import Image from "next/image";
import Link from "next/link";
import { notFound } from "next/navigation";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog";
import { Separator } from "@/components/ui/separator";
import { useMobil, useFotoMobils, useStokMobils } from "@/lib/hooks";
import { getImageUrl } from "@/lib/api";
import { formatCurrency, formatNumber } from "@/lib/utils";
import {
    Calendar,
    Fuel,
    Gauge,
    Settings,
    MapPin,
    Phone,
    MessageCircle,
    Share2,
    Heart,
    ChevronLeft,
    ChevronRight,
    Play,
    Eye,
    Car,
    FileText,
    Shield,
    Award,
} from "lucide-react";
import { toast } from "sonner";

interface CarDetailPageProps {
    carId: string;
}

export default function CarDetailPage({ carId }: CarDetailPageProps) {
    const [currentImageIndex, setCurrentImageIndex] = useState(0);
    const [selectedVariant, setSelectedVariant] = useState<string>("");
    const [isImageModalOpen, setIsImageModalOpen] = useState(false);
    const [isFavorite, setIsFavorite] = useState(false);
    const {
        data: car,
        isLoading: carLoading,
        error: carError,
    } = useMobil(carId);
    const { data: photos, isLoading: photosLoading } = useFotoMobils(carId);
    const { data: stock, isLoading: stockLoading } = useStokMobils(carId);

    if (carError) {
        notFound();
    }

    if (carLoading || photosLoading || stockLoading) {
        return <CarDetailSkeleton />;
    }
    if (!car || !(car as any)?.data) {
        notFound();
    }

    const carData = (car as any).data;
    const images = ((photos as any)?.data || []) as any[];
    const variants = ((stock as any)?.data || []) as any[];
    const selectedVariantData =
        variants.find((v: any) => v.id.toString() === selectedVariant) ||
        variants[0];

    const nextImage = () => {
        setCurrentImageIndex((prev) => (prev + 1) % images.length);
    };

    const prevImage = () => {
        setCurrentImageIndex(
            (prev) => (prev - 1 + images.length) % images.length
        );
    };

    const handleShare = async () => {
        if (navigator.share) {
            try {
                await navigator.share({
                    title: `${carData.nama} - ${carData.merek?.nama}`,
                    text: `Lihat mobil bekas ${carData.nama} di Toko Jaya Motor`,
                    url: window.location.href,
                });
            } catch (error) {
                console.error("Error sharing:", error);
            }
        } else {
            navigator.clipboard.writeText(window.location.href);
            toast.success("Link berhasil disalin!");
        }
    };

    const handleFavorite = () => {
        setIsFavorite(!isFavorite);
        toast.success(
            isFavorite ? "Dihapus dari favorit" : "Ditambahkan ke favorit"
        );
    };

    const handleAppointment = () => {
        // Navigate to appointment page with car data
        const url = `/janji-temu?mobil=${carId}&varian=${selectedVariant}`;
        window.open(url, "_blank");
    };

    const handleContact = () => {
        const message = `Halo, saya tertarik dengan ${carData.nama} ${selectedVariantData?.varian}. Bisakah Anda memberikan informasi lebih lanjut?`;
        const whatsappUrl = `https://wa.me/628123456789?text=${encodeURIComponent(
            message
        )}`;
        window.open(whatsappUrl, "_blank");
    };

    return (
        <div className="min-h-screen bg-gray-50">
            <div className="container mx-auto px-4 py-8">
                {/* Breadcrumb */}
                <nav className="text-sm text-gray-600 mb-6">
                    <Link href="/" className="hover:text-primary">
                        Beranda
                    </Link>
                    <span className="mx-2">/</span>
                    <Link href="/mobil" className="hover:text-primary">
                        Katalog
                    </Link>
                    <span className="mx-2">/</span>
                    <Link
                        href={`/merek/${carData.merek?.id}`}
                        className="hover:text-primary"
                    >
                        {" "}
                        {carData.merek?.nama}
                    </Link>
                    <span className="mx-2">/</span>
                    <span className="text-gray-900">{carData.nama}</span>
                </nav>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {/* Image Gallery */}
                    <div className="lg:col-span-2">
                        <Card>
                            <CardContent className="p-0">
                                <div className="relative">
                                    {images.length > 0 ? (
                                        <>
                                            <div className="relative h-96 bg-gray-100 rounded-t-lg overflow-hidden">
                                                <Image
                                                    src={
                                                        getImageUrl(
                                                            images[
                                                                currentImageIndex
                                                            ]?.path_file
                                                        ) ||
                                                        "/images/car-placeholder.jpg"
                                                    }
                                                    alt={`${
                                                        carData.nama
                                                    } - Photo ${
                                                        currentImageIndex + 1
                                                    }`}
                                                    fill
                                                    className="object-cover cursor-pointer"
                                                    onClick={() =>
                                                        setIsImageModalOpen(
                                                            true
                                                        )
                                                    }
                                                />

                                                {/* Image Navigation */}
                                                {images.length > 1 && (
                                                    <>
                                                        <Button
                                                            variant="secondary"
                                                            size="icon"
                                                            className="absolute left-4 top-1/2 transform -translate-y-1/2"
                                                            onClick={prevImage}
                                                        >
                                                            <ChevronLeft className="w-4 h-4" />
                                                        </Button>
                                                        <Button
                                                            variant="secondary"
                                                            size="icon"
                                                            className="absolute right-4 top-1/2 transform -translate-y-1/2"
                                                            onClick={nextImage}
                                                        >
                                                            <ChevronRight className="w-4 h-4" />
                                                        </Button>
                                                    </>
                                                )}

                                                {/* Image Counter */}
                                                <div className="absolute bottom-4 right-4 bg-black/50 text-white px-3 py-1 rounded-full text-sm">
                                                    {currentImageIndex + 1} /{" "}
                                                    {images.length}
                                                </div>

                                                {/* View Full Gallery */}
                                                <Button
                                                    variant="secondary"
                                                    size="sm"
                                                    className="absolute bottom-4 left-4"
                                                    onClick={() =>
                                                        setIsImageModalOpen(
                                                            true
                                                        )
                                                    }
                                                >
                                                    <Eye className="w-4 h-4 mr-2" />
                                                    Lihat Semua Foto
                                                </Button>
                                            </div>

                                            {/* Thumbnail Gallery */}
                                            {images.length > 1 && (
                                                <div className="p-4">
                                                    <div className="flex gap-2 overflow-x-auto">
                                                        {images.map(
                                                            (image, index) => (
                                                                <button
                                                                    key={
                                                                        image.id
                                                                    }
                                                                    className={`relative w-20 h-16 flex-shrink-0 rounded-lg overflow-hidden border-2 ${
                                                                        index ===
                                                                        currentImageIndex
                                                                            ? "border-primary"
                                                                            : "border-gray-200"
                                                                    }`}
                                                                    onClick={() =>
                                                                        setCurrentImageIndex(
                                                                            index
                                                                        )
                                                                    }
                                                                >
                                                                    <Image
                                                                        src={
                                                                            image.url_foto
                                                                        }
                                                                        alt={`Thumbnail ${
                                                                            index +
                                                                            1
                                                                        }`}
                                                                        fill
                                                                        className="object-cover"
                                                                    />
                                                                </button>
                                                            )
                                                        )}
                                                    </div>
                                                </div>
                                            )}
                                        </>
                                    ) : (
                                        <div className="h-96 bg-gray-100 rounded-t-lg flex items-center justify-center">
                                            <div className="text-center text-gray-500">
                                                <Car className="w-16 h-16 mx-auto mb-2" />
                                                <p>Foto tidak tersedia</p>
                                            </div>
                                        </div>
                                    )}
                                </div>
                            </CardContent>
                        </Card>

                        {/* Car Details Tabs */}
                        <Card className="mt-6">
                            <CardContent className="p-6">
                                <Tabs
                                    defaultValue="overview"
                                    className="w-full"
                                >
                                    <TabsList className="grid w-full grid-cols-4">
                                        <TabsTrigger value="overview">
                                            Overview
                                        </TabsTrigger>
                                        <TabsTrigger value="specs">
                                            Spesifikasi
                                        </TabsTrigger>
                                        <TabsTrigger value="variants">
                                            Varian
                                        </TabsTrigger>
                                        <TabsTrigger value="history">
                                            Riwayat
                                        </TabsTrigger>
                                    </TabsList>

                                    <TabsContent
                                        value="overview"
                                        className="mt-6"
                                    >
                                        <div className="space-y-4">
                                            <h3 className="text-lg font-semibold">
                                                Deskripsi
                                            </h3>
                                            <p className="text-gray-600 leading-relaxed">
                                                {carData.deskripsi ||
                                                    "Deskripsi tidak tersedia."}
                                            </p>

                                            <h4 className="text-md font-semibold mt-6">
                                                Fitur Unggulan
                                            </h4>
                                            <div className="grid grid-cols-2 gap-4">
                                                <div className="flex items-center gap-2">
                                                    <Shield className="w-5 h-5 text-primary" />
                                                    <span>Kondisi Terawat</span>
                                                </div>
                                                <div className="flex items-center gap-2">
                                                    <Award className="w-5 h-5 text-primary" />
                                                    <span>
                                                        Garansi Kualitas
                                                    </span>
                                                </div>
                                                <div className="flex items-center gap-2">
                                                    <FileText className="w-5 h-5 text-primary" />
                                                    <span>Dokumen Lengkap</span>
                                                </div>
                                                <div className="flex items-center gap-2">
                                                    <Car className="w-5 h-5 text-primary" />
                                                    <span>Siap Pakai</span>
                                                </div>
                                            </div>
                                        </div>
                                    </TabsContent>

                                    <TabsContent value="specs" className="mt-6">
                                        <div className="space-y-4">
                                            <h3 className="text-lg font-semibold">
                                                Spesifikasi Teknis
                                            </h3>
                                            {carData.spesifikasi ? (
                                                <pre className="text-gray-600 whitespace-pre-wrap">
                                                    {carData.spesifikasi}
                                                </pre>
                                            ) : (
                                                <p className="text-gray-500">
                                                    Spesifikasi detail akan
                                                    segera tersedia.
                                                </p>
                                            )}
                                        </div>
                                    </TabsContent>

                                    <TabsContent
                                        value="variants"
                                        className="mt-6"
                                    >
                                        <div className="space-y-4">
                                            <h3 className="text-lg font-semibold">
                                                Varian Tersedia
                                            </h3>
                                            <div className="grid gap-4">
                                                {variants.map((variant) => (
                                                    <Card
                                                        key={variant.id}
                                                        className={`cursor-pointer border-2 ${
                                                            selectedVariant ===
                                                            variant.id.toString()
                                                                ? "border-primary"
                                                                : "border-gray-200"
                                                        }`}
                                                    >
                                                        <CardContent className="p-4">
                                                            <div className="flex justify-between items-start">
                                                                <div>
                                                                    <h4 className="font-semibold">
                                                                        {
                                                                            variant
                                                                                .varian
                                                                                ?.nama
                                                                        }
                                                                    </h4>
                                                                    <p className="text-2xl font-bold text-primary mt-1">
                                                                        {formatCurrency(
                                                                            variant.harga_jual
                                                                        )}
                                                                    </p>
                                                                    <div className="flex gap-4 mt-2 text-sm text-gray-600">
                                                                        <span>
                                                                            {
                                                                                variant.warna
                                                                            }
                                                                        </span>
                                                                        <span>
                                                                            {
                                                                                variant
                                                                                    .varian
                                                                                    ?.transmisi
                                                                            }
                                                                        </span>
                                                                        <span>
                                                                            {
                                                                                variant
                                                                                    .varian
                                                                                    ?.jenis_bahan_bakar
                                                                            }
                                                                        </span>
                                                                        {variant.kilometer && (
                                                                            <span>
                                                                                {formatNumber(
                                                                                    variant.kilometer
                                                                                )}{" "}
                                                                                km
                                                                            </span>
                                                                        )}
                                                                    </div>
                                                                </div>
                                                                <Badge
                                                                    variant={
                                                                        variant.status ===
                                                                        "tersedia"
                                                                            ? "default"
                                                                            : "secondary"
                                                                    }
                                                                >
                                                                    {
                                                                        variant.status
                                                                    }
                                                                </Badge>
                                                            </div>
                                                        </CardContent>
                                                    </Card>
                                                ))}
                                            </div>
                                        </div>
                                    </TabsContent>

                                    <TabsContent
                                        value="history"
                                        className="mt-6"
                                    >
                                        <div className="space-y-4">
                                            <h3 className="text-lg font-semibold">
                                                Riwayat Perawatan
                                            </h3>
                                            {selectedVariantData?.riwayat_servis
                                                ?.length ? (
                                                <div className="space-y-3">
                                                    {selectedVariantData.riwayat_servis.map(
                                                        (service: any) => (
                                                            <Card
                                                                key={service.id}
                                                            >
                                                                <CardContent className="p-4">
                                                                    <div className="flex justify-between items-start">
                                                                        <div>
                                                                            <h4 className="font-medium">
                                                                                {
                                                                                    service.jenis_servis
                                                                                }
                                                                            </h4>
                                                                            <p className="text-sm text-gray-600">
                                                                                {
                                                                                    service.deskripsi
                                                                                }
                                                                            </p>
                                                                            <p className="text-sm text-gray-500 mt-1">
                                                                                {new Date(
                                                                                    service.tanggal_servis
                                                                                ).toLocaleDateString(
                                                                                    "id-ID"
                                                                                )}
                                                                                {service.bengkel &&
                                                                                    ` - ${service.bengkel}`}
                                                                            </p>
                                                                        </div>
                                                                        {service.biaya && (
                                                                            <span className="text-sm font-medium">
                                                                                {formatCurrency(
                                                                                    service.biaya
                                                                                )}
                                                                            </span>
                                                                        )}
                                                                    </div>
                                                                </CardContent>
                                                            </Card>
                                                        )
                                                    )}
                                                </div>
                                            ) : (
                                                <p className="text-gray-500">
                                                    Riwayat perawatan akan
                                                    tersedia setelah memilih
                                                    varian.
                                                </p>
                                            )}
                                        </div>
                                    </TabsContent>
                                </Tabs>
                            </CardContent>
                        </Card>
                    </div>

                    {/* Sidebar */}
                    <div className="space-y-6">
                        {/* Car Info Card */}
                        <Card>
                            <CardHeader>
                                <div className="flex justify-between items-start">
                                    <div>
                                        {" "}
                                        <CardTitle className="text-xl">
                                            {carData.nama}
                                        </CardTitle>
                                        <p className="text-gray-600">
                                            {carData.merek?.nama} •{" "}
                                            {carData.tahun_mulai}
                                        </p>
                                    </div>
                                    <div className="flex gap-2">
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            onClick={handleShare}
                                        >
                                            <Share2 className="w-4 h-4" />
                                        </Button>
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            onClick={handleFavorite}
                                            className={
                                                isFavorite ? "text-red-500" : ""
                                            }
                                        >
                                            <Heart
                                                className={`w-4 h-4 ${
                                                    isFavorite
                                                        ? "fill-current"
                                                        : ""
                                                }`}
                                            />
                                        </Button>
                                    </div>
                                </div>
                            </CardHeader>
                            <CardContent>
                                {selectedVariantData && (
                                    <>
                                        {" "}
                                        <div className="text-3xl font-bold text-primary mb-4">
                                            {formatCurrency(
                                                selectedVariantData.harga_jual
                                            )}
                                        </div>
                                        {/* Quick Specs */}
                                        <div className="grid grid-cols-2 gap-4 mb-6">
                                            <div className="flex items-center gap-2">
                                                <Calendar className="w-4 h-4 text-gray-500" />{" "}
                                                <span className="text-sm">
                                                    {carData.tahun_mulai}
                                                </span>
                                            </div>{" "}
                                            <div className="flex items-center gap-2">
                                                <Settings className="w-4 h-4 text-gray-500" />
                                                <span className="text-sm">
                                                    {selectedVariantData.varian
                                                        ?.transmisi || "N/A"}
                                                </span>
                                            </div>
                                            <div className="flex items-center gap-2">
                                                <Fuel className="w-4 h-4 text-gray-500" />
                                                <span className="text-sm">
                                                    {selectedVariantData.varian
                                                        ?.jenis_bahan_bakar ||
                                                        "N/A"}
                                                </span>
                                            </div>
                                            {selectedVariantData.kilometer && (
                                                <div className="flex items-center gap-2">
                                                    <Gauge className="w-4 h-4 text-gray-500" />
                                                    <span className="text-sm">
                                                        {formatNumber(
                                                            selectedVariantData.kilometer
                                                        )}{" "}
                                                        km
                                                    </span>
                                                </div>
                                            )}
                                        </div>
                                        <Separator className="my-4" />
                                        {/* Action Buttons */}
                                        <div className="space-y-3">
                                            <Button
                                                className="w-full"
                                                size="lg"
                                                onClick={handleAppointment}
                                                disabled={
                                                    selectedVariantData.status !==
                                                    "tersedia"
                                                }
                                            >
                                                <Calendar className="w-4 h-4 mr-2" />
                                                Buat Janji Temu
                                            </Button>

                                            <Button
                                                variant="outline"
                                                className="w-full"
                                                size="lg"
                                                onClick={handleContact}
                                            >
                                                <MessageCircle className="w-4 h-4 mr-2" />
                                                Hubungi via WhatsApp
                                            </Button>

                                            <Button
                                                variant="outline"
                                                className="w-full"
                                                size="lg"
                                            >
                                                <Phone className="w-4 h-4 mr-2" />
                                                Telepon Langsung
                                            </Button>
                                        </div>
                                    </>
                                )}

                                {variants.length > 1 && (
                                    <>
                                        <Separator className="my-4" />
                                        <div>
                                            <label className="text-sm font-medium mb-2 block">
                                                Pilih Varian:
                                            </label>
                                            <select
                                                value={selectedVariant}
                                                onChange={(e) =>
                                                    setSelectedVariant(
                                                        e.target.value
                                                    )
                                                }
                                                className="w-full p-2 border rounded-md"
                                            >
                                                {variants.map((variant) => (
                                                    <option
                                                        key={variant.id}
                                                        value={variant.id.toString()}
                                                    >
                                                        {variant.varian?.nama ||
                                                            `Varian ${variant.id}`}{" "}
                                                        -{" "}
                                                        {formatCurrency(
                                                            variant.harga_jual
                                                        )}
                                                    </option>
                                                ))}
                                            </select>
                                        </div>
                                    </>
                                )}
                            </CardContent>
                        </Card>

                        {/* Showroom Info */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="text-lg">
                                    Lokasi Showroom
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-3">
                                    <div className="flex items-start gap-3">
                                        <MapPin className="w-5 h-5 text-primary mt-0.5" />
                                        <div>
                                            <p className="font-medium">
                                                Toko Jaya Motor
                                            </p>
                                            <p className="text-sm text-gray-600">
                                                Jl. Raya Utama No. 123
                                                <br />
                                                Jakarta Selatan, DKI Jakarta
                                                12345
                                            </p>
                                        </div>
                                    </div>

                                    <div className="flex items-center gap-3">
                                        <Phone className="w-5 h-5 text-primary" />
                                        <div>
                                            <p className="font-medium">
                                                +62 812-3456-7890
                                            </p>
                                            <p className="text-sm text-gray-600">
                                                Senin - Sabtu: 08:00 - 17:00
                                            </p>
                                        </div>
                                    </div>

                                    <Button
                                        variant="outline"
                                        className="w-full mt-4"
                                    >
                                        <MapPin className="w-4 h-4 mr-2" />
                                        Lihat di Maps
                                    </Button>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>

                {/* Image Modal */}
                <Dialog
                    open={isImageModalOpen}
                    onOpenChange={setIsImageModalOpen}
                >
                    <DialogContent className="max-w-4xl">
                        <DialogHeader>
                            {" "}
                            <DialogTitle>
                                Galeri Foto - {carData.nama}
                            </DialogTitle>
                        </DialogHeader>
                        <div className="grid grid-cols-2 md:grid-cols-3 gap-4 max-h-96 overflow-y-auto">
                            {images.map((image, index) => (
                                <div
                                    key={image.id}
                                    className="relative aspect-video"
                                >
                                    <Image
                                        src={getImageUrl(image.path_file)}
                                        alt={`${carData.nama} - Photo ${
                                            index + 1
                                        }`}
                                        fill
                                        className="object-cover rounded-lg"
                                    />
                                </div>
                            ))}
                        </div>
                    </DialogContent>
                </Dialog>
            </div>
        </div>
    );
}

// Loading skeleton component
function CarDetailSkeleton() {
    return (
        <div className="min-h-screen bg-gray-50">
            <div className="container mx-auto px-4 py-8">
                <div className="h-4 w-64 bg-gray-200 rounded mb-6 animate-pulse"></div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div className="lg:col-span-2 space-y-6">
                        <Card>
                            <div className="h-96 bg-gray-200 rounded-t-lg animate-pulse"></div>
                            <CardContent className="p-4">
                                <div className="flex gap-2">
                                    {Array.from({ length: 5 }).map((_, i) => (
                                        <div
                                            key={i}
                                            className="w-20 h-16 bg-gray-200 rounded animate-pulse"
                                        ></div>
                                    ))}
                                </div>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardContent className="p-6">
                                <div className="h-8 w-48 bg-gray-200 rounded mb-4 animate-pulse"></div>
                                <div className="space-y-2">
                                    <div className="h-4 w-full bg-gray-200 rounded animate-pulse"></div>
                                    <div className="h-4 w-3/4 bg-gray-200 rounded animate-pulse"></div>
                                    <div className="h-4 w-1/2 bg-gray-200 rounded animate-pulse"></div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    <div className="space-y-6">
                        <Card>
                            <CardHeader>
                                <div className="h-6 w-48 bg-gray-200 rounded animate-pulse"></div>
                                <div className="h-4 w-32 bg-gray-200 rounded animate-pulse"></div>
                            </CardHeader>
                            <CardContent>
                                <div className="h-8 w-32 bg-gray-200 rounded mb-4 animate-pulse"></div>
                                <div className="space-y-3">
                                    {Array.from({ length: 3 }).map((_, i) => (
                                        <div
                                            key={i}
                                            className="h-10 w-full bg-gray-200 rounded animate-pulse"
                                        ></div>
                                    ))}
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </div>
    );
}
