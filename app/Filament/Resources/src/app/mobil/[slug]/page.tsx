import { Metadata } from "next";
import { notFound } from "next/navigation";
import { getCarDetailSEO } from "@/lib/seo";
import CarDetailPage from "@/components/car/car-detail-page";

interface Props {
    params: {
        slug: string;
    };
}

// Generate metadata for SEO
export async function generateMetadata({ params }: Props): Promise<Metadata> {
    // Extract car ID from slug (format: car-name-year-id)
    const id = params.slug.split("-").pop();

    try {
        // In real app, fetch car data here
        // const car = await fetchCarById(id)
        const mockCar = {
            nama: "Toyota Avanza",
            merek: { nama: "Toyota" },
            harga: 150000000,
            deskripsi: "Mobil keluarga yang nyaman dan irit bahan bakar",
        };

        return getCarDetailSEO(mockCar);
    } catch {
        return {
            title: "Mobil Tidak Ditemukan | Toko Jaya Motor",
            description: "Mobil yang Anda cari tidak tersedia.",
        };
    }
}

export default function CarDetailPageRoute({ params }: Props) {
    // Extract car ID from slug
    const carId = params.slug.split("-").pop();

    if (!carId || isNaN(Number(carId))) {
        notFound();
    }

    return <CarDetailPage carId={carId} />;
}
