import Link from "next/link";
import Image from "next/image";
import { Card, CardContent, CardFooter } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Calendar, Fuel, Settings, Eye, Car } from "lucide-react";
import { formatPrice, getCarSlug } from "@/lib/hooks";
import { getImageUrl } from "@/lib/api";
import type { Mobil, StokMobil } from "@/lib/types";

interface CarCardProps {
    car: Mobil;
    stockItem?: StokMobil;
    className?: string;
}

export default function CarCard({ car, stockItem, className }: CarCardProps) {
    const primaryPhoto =
        car.foto_mobils?.find((foto) => foto.is_primary) ||
        car.foto_mobils?.[0];
    const carSlug = getCarSlug(car);
    const lowestPrice =
        stockItem?.harga_jual || car.stok_mobils?.[0]?.harga_jual;

    return (
        <Card
            className={`group hover:shadow-lg transition-shadow duration-300 ${className}`}
        >
            <div className="relative overflow-hidden rounded-t-lg">
                <div className="aspect-[4/3] relative">
                    {primaryPhoto ? (
                        <Image
                            src={getImageUrl(primaryPhoto.path_file)}
                            alt={`${car.nama} ${car.tahun_mulai}`}
                            fill
                            className="object-cover group-hover:scale-105 transition-transform duration-300"
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
                            stockItem?.status === "tersedia"
                                ? "default"
                                : "secondary"
                        }
                    >
                        {stockItem?.status === "tersedia"
                            ? "Tersedia"
                            : stockItem?.status === "terjual"
                            ? "Terjual"
                            : "Reserved"}
                    </Badge>
                </div>

                {/* Condition Badge */}
                {stockItem?.kondisi && (
                    <div className="absolute top-3 right-3">
                        <Badge
                            variant={
                                stockItem.kondisi === "baru"
                                    ? "default"
                                    : "outline"
                            }
                        >
                            {stockItem.kondisi === "baru" ? "Baru" : "Bekas"}
                        </Badge>
                    </div>
                )}

                {/* View Overlay */}
                <div className="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                    <Button
                        asChild
                        size="sm"
                        className="bg-white text-black hover:bg-gray-100"
                    >
                        <Link href={`/mobil/${carSlug}`}>
                            <Eye className="mr-2 h-4 w-4" />
                            Lihat Detail
                        </Link>
                    </Button>
                </div>
            </div>

            <CardContent className="p-4">
                <div className="space-y-2">
                    {" "}
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
                    </h3>
                    {/* Year and Price */}
                    <div className="flex items-center justify-between">
                        {" "}
                        <div className="flex items-center gap-1 text-sm text-muted-foreground">
                            <Calendar className="h-4 w-4" />
                            <span>{car.tahun_mulai}</span>
                        </div>{" "}
                        {lowestPrice && (
                            <div className="text-right">
                                <p className="text-lg font-bold text-primary">
                                    {formatPrice(lowestPrice)}
                                </p>
                            </div>
                        )}
                    </div>
                    {/* Specifications */}
                    {stockItem && (
                        <div className="flex items-center gap-4 text-sm text-muted-foreground">
                            {stockItem.varian && (
                                <>
                                    {stockItem.varian.transmisi && (
                                        <div className="flex items-center gap-1">
                                            <Settings className="h-4 w-4" />
                                            <span className="capitalize">
                                                {stockItem.varian.transmisi}
                                            </span>
                                        </div>
                                    )}
                                    {stockItem.varian.jenis_bahan_bakar && (
                                        <div className="flex items-center gap-1">
                                            <Fuel className="h-4 w-4" />
                                            <span className="capitalize">
                                                {
                                                    stockItem.varian
                                                        .jenis_bahan_bakar
                                                }
                                            </span>
                                        </div>
                                    )}
                                </>
                            )}
                            {stockItem.kilometer && (
                                <span>
                                    {stockItem.kilometer.toLocaleString()} km
                                </span>
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
                {" "}
                <Button asChild className="flex-1">
                    <Link href={`/mobil/${carSlug}`}>Lihat Detail</Link>
                </Button>
                <Button variant="outline" asChild>
                    <Link href={`/appointment?car=${car.id}`}>Janji Temu</Link>
                </Button>{" "}
            </CardFooter>
        </Card>
    );
}
