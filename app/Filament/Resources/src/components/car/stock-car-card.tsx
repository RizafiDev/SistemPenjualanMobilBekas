import Link from "next/link";
import Image from "next/image";
import { Card, CardContent, CardFooter } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import {
    Calendar,
    Fuel,
    Settings,
    Eye,
    Car,
    Palette,
    Gauge,
} from "lucide-react";
import { formatPrice, getCarSlug } from "@/lib/hooks";
import { getImageUrl } from "@/lib/api";
import type { StokMobil } from "@/lib/types";

interface StockCarCardProps {
    stockCar: StokMobil;
    className?: string;
}

export default function StockCarCard({
    stockCar,
    className,
}: StockCarCardProps) {
    const car = stockCar.mobil;
    const varian = stockCar.varian;
    if (!car) {
        return null; // Skip if no car data
    }

    const primaryPhoto =
        car.foto_mobils?.find((foto: any) => foto.is_primary) ||
        car.foto_mobils?.[0];

    const carSlug = getCarSlug(car);    return (
        <Card
            className={`group hover:shadow-2xl transition-all duration-300 border-0 shadow-lg overflow-hidden bg-white hover:scale-[1.02] ${className}`}
        >
            <div className="relative overflow-hidden">
                <div className="aspect-[4/3] relative">
                    {primaryPhoto ? (
                        <Image
                            src={getImageUrl(primaryPhoto.path_file)}
                            alt={`${car.nama} ${car.tahun_mulai} - ${stockCar.warna}`}
                            fill
                            className="object-cover group-hover:scale-110 transition-transform duration-500"
                            sizes="(min-width: 1024px) 25vw, (min-width: 768px) 33vw, (min-width: 640px) 50vw, 100vw"
                        />
                    ) : (
                        <div className="w-full h-full bg-muted flex items-center justify-center">
                            <div className="text-center text-muted-foreground">
                                <Car className="mx-auto h-12 w-12 mb-2" />
                                <p className="text-sm">Foto tidak tersedia</p>
                            </div>
                        </div>
                    )}
                </div>

                {/* Status Badge */}
                <div className="absolute top-3 left-3">
                    <Badge
                        variant={
                            stockCar.status === "tersedia"
                                ? "default"
                                : "secondary"
                        }
                    >
                        {stockCar.status === "tersedia"
                            ? "Tersedia"
                            : stockCar.status === "terjual"
                            ? "Terjual"
                            : "Reserved"}
                    </Badge>
                </div>

                {/* Condition Badge */}
                <div className="absolute top-3 right-3">
                    <Badge
                        variant={
                            stockCar.kondisi === "baru" ? "default" : "outline"
                        }
                    >
                        {stockCar.kondisi === "baru" ? "Baru" : "Bekas"}
                    </Badge>
                </div>

                {/* View Overlay */}
                <div className="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                    <Button
                        asChild
                        size="sm"
                        className="bg-white text-black hover:bg-gray-100"
                    >
                        <Link href={`/mobil/${carSlug}?stock=${stockCar.id}`}>
                            <Eye className="mr-2 h-4 w-4" />
                            Lihat Detail
                        </Link>
                    </Button>
                </div>
            </div>

            <CardContent className="p-4">
                <div className="space-y-2">
                    {/* Brand and Model */}
                    <div className="flex items-center justify-between">
                        <span className="text-sm text-muted-foreground font-medium">
                            {car.merek?.nama}
                        </span>
                        <span className="text-sm text-muted-foreground">
                            {car.kategori?.nama}
                        </span>
                    </div>

                    {/* Car Name */}
                    <h3 className="font-semibold text-lg line-clamp-1">
                        {car.nama}
                        {varian && (
                            <span className="text-sm font-normal text-muted-foreground ml-2">
                                {varian.nama}
                            </span>
                        )}
                    </h3>

                    {/* Year, Color and Price */}
                    <div className="flex items-center justify-between">
                        <div className="flex items-center gap-3 text-sm text-muted-foreground">
                            <div className="flex items-center gap-1">
                                <Calendar className="h-4 w-4" />
                                <span>{stockCar.tahun || car.tahun_mulai}</span>
                            </div>
                            <div className="flex items-center gap-1">
                                <Palette className="h-4 w-4" />
                                <span className="capitalize">
                                    {stockCar.warna}
                                </span>
                            </div>
                        </div>
                        <div className="text-right">
                            <p className="text-lg font-bold text-primary">
                                {formatPrice(stockCar.harga_jual)}
                            </p>
                        </div>
                    </div>

                    {/* Specifications from Varian */}
                    {varian && (
                        <div className="flex items-center gap-4 text-sm text-muted-foreground">
                            {varian.transmisi && (
                                <div className="flex items-center gap-1">
                                    <Settings className="h-4 w-4" />
                                    <span className="capitalize">
                                        {varian.transmisi}
                                    </span>
                                </div>
                            )}
                            {varian.jenis_bahan_bakar && (
                                <div className="flex items-center gap-1">
                                    <Fuel className="h-4 w-4" />
                                    <span className="capitalize">
                                        {varian.jenis_bahan_bakar}
                                    </span>
                                </div>
                            )}
                            {stockCar.kilometer && (
                                <div className="flex items-center gap-1">
                                    <Gauge className="h-4 w-4" />
                                    <span>
                                        {stockCar.kilometer.toLocaleString()} km
                                    </span>
                                </div>
                            )}
                        </div>
                    )}

                    {/* Description */}
                    {car.deskripsi && (
                        <p className="text-sm text-muted-foreground line-clamp-2">
                            {car.deskripsi}
                        </p>
                    )}
                </div>
            </CardContent>

            <CardFooter className="p-4 pt-0 flex gap-2">
                <Button asChild className="flex-1">
                    <Link href={`/mobil/${carSlug}?stock=${stockCar.id}`}>
                        Lihat Detail
                    </Link>
                </Button>
                <Button variant="outline" asChild>
                    <Link href={`/janji-temu?stock=${stockCar.id}`}>
                        Janji Temu
                    </Link>
                </Button>
            </CardFooter>
        </Card>
    );
}
