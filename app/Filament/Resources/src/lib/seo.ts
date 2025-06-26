import { DefaultSeoProps } from "next-seo";
import { Metadata } from "next";

export const defaultSEO: DefaultSeoProps = {
    title: "Mobil Bekas Berkualitas | Sistem Penjualan Mobil Bekas",
    description:
        "Temukan mobil bekas berkualitas dengan harga terbaik. Berbagai merek dan model tersedia dengan garansi dan pelayanan terpercaya.",
    canonical: "https://mobilbekas.com",
    openGraph: {
        type: "website",
        locale: "id_ID",
        url: "https://mobilbekas.com",
        siteName: "Mobil Bekas",
        title: "Mobil Bekas Berkualitas | Sistem Penjualan Mobil Bekas",
        description:
            "Temukan mobil bekas berkualitas dengan harga terbaik. Berbagai merek dan model tersedia dengan garansi dan pelayanan terpercaya.",
        images: [
            {
                url: "/images/og-image.jpg",
                width: 1200,
                height: 630,
                alt: "Mobil Bekas Berkualitas",
            },
        ],
    },
    twitter: {
        handle: "@mobilbekas",
        site: "@mobilbekas",
        cardType: "summary_large_image",
    },
    additionalMetaTags: [
        {
            name: "viewport",
            content: "width=device-width, initial-scale=1",
        },
        {
            name: "robots",
            content: "index,follow",
        },
        {
            name: "author",
            content: "Mobil Bekas Indonesia",
        },
        {
            name: "keywords",
            content:
                "mobil bekas, jual beli mobil, mobil second, Toyota bekas, Honda bekas, Suzuki bekas",
        },
    ],
};

export const generateCarSEO = (car: any) => ({
    title: `${car.nama_mobil} ${car.tahun_produksi} - Mobil Bekas Berkualitas`,
    description: `${car.nama_mobil} ${
        car.tahun_produksi
    } bekas berkualitas dengan harga terbaik. ${
        car.deskripsi || "Kondisi prima dan terawat."
    }`,
    canonical: `https://mobilbekas.com/car/${car.id}`,
    openGraph: {
        title: `${car.nama_mobil} ${car.tahun_produksi} - Mobil Bekas`,
        description: `${car.nama_mobil} ${
            car.tahun_produksi
        } bekas berkualitas dengan harga terbaik. ${
            car.deskripsi || "Kondisi prima dan terawat."
        }`,
        url: `https://mobilbekas.com/car/${car.id}`,
        images:
            car.foto_mobils?.length > 0
                ? [
                      {
                          url: car.foto_mobils[0].url_foto,
                          width: 800,
                          height: 600,
                          alt: `${car.nama_mobil} ${car.tahun_produksi}`,
                      },
                  ]
                : [],
    },
});

export const generateBrandSEO = (brand: any) => ({
    title: `${brand.nama_merek} Bekas - Mobil Berkualitas`,
    description: `Koleksi mobil bekas ${
        brand.nama_merek
    } berkualitas dengan harga terbaik. ${
        brand.deskripsi || "Berbagai model dan tahun tersedia."
    }`,
    canonical: `https://mobilbekas.com/brand/${brand.id}`,
    openGraph: {
        title: `${brand.nama_merek} Bekas - Mobil Berkualitas`,
        description: `Koleksi mobil bekas ${
            brand.nama_merek
        } berkualitas dengan harga terbaik. ${
            brand.deskripsi || "Berbagai model dan tahun tersedia."
        }`,
        url: `https://mobilbekas.com/brand/${brand.id}`,
    },
});

export const generateCategorySEO = (category: any) => ({
    title: `Mobil ${category.nama_kategori} Bekas - Pilihan Terbaik`,
    description: `Temukan mobil ${
        category.nama_kategori
    } bekas berkualitas dengan berbagai pilihan merek dan model. ${
        category.deskripsi || "Harga kompetitif dan kondisi prima."
    }`,
    canonical: `https://mobilbekas.com/category/${category.id}`,
    openGraph: {
        title: `Mobil ${category.nama_kategori} Bekas - Pilihan Terbaik`,
        description: `Temukan mobil ${
            category.nama_kategori
        } bekas berkualitas dengan berbagai pilihan merek dan model. ${
            category.deskripsi || "Harga kompetitif dan kondisi prima."
        }`,
        url: `https://mobilbekas.com/category/${category.id}`,
    },
});

export const generateCarJsonLd = (car: any, stockItem?: any) => ({
    "@context": "https://schema.org",
    "@type": "Car",
    name: `${car.nama_mobil} ${car.tahun_produksi}`,
    description: car.deskripsi,
    brand: {
        "@type": "Brand",
        name: car.merek?.nama_merek,
    },
    model: car.nama_mobil,
    productionDate: car.tahun_produksi,
    vehicleTransmission: stockItem?.transmisi,
    fuelType: stockItem?.bahan_bakar,
    offers: stockItem
        ? {
              "@type": "Offer",
              priceCurrency: "IDR",
              price: stockItem.harga,
              availability:
                  stockItem.status === "tersedia"
                      ? "https://schema.org/InStock"
                      : "https://schema.org/OutOfStock",
              seller: {
                  "@type": "Organization",
                  name: "Mobil Bekas Indonesia",
              },
          }
        : undefined,
    image: car.foto_mobils?.map((foto: any) => foto.url_foto) || [],
});

export const getCatalogSEO = (): Metadata => ({
    title: "Katalog Mobil Bekas | Toko Jaya Motor",
    description:
        "Jelajahi koleksi lengkap mobil bekas berkualitas dengan berbagai merek dan kategori. Temukan mobil impian Anda dengan harga terbaik di Toko Jaya Motor.",
    keywords:
        "katalog mobil bekas, mobil second, jual beli mobil bekas, daftar mobil bekas",
    openGraph: {
        title: "Katalog Mobil Bekas | Toko Jaya Motor",
        description:
            "Jelajahi koleksi lengkap mobil bekas berkualitas dengan berbagai merek dan kategori.",
        type: "website",
        url: "/mobil",
        images: [
            {
                url: "/images/catalog-og.jpg",
                width: 1200,
                height: 630,
                alt: "Katalog Mobil Bekas Toko Jaya Motor",
            },
        ],
    },
    twitter: {
        card: "summary_large_image",
        title: "Katalog Mobil Bekas | Toko Jaya Motor",
        description:
            "Jelajahi koleksi lengkap mobil bekas berkualitas dengan berbagai merek dan kategori.",
        images: ["/images/catalog-og.jpg"],
    },
    alternates: {
        canonical: "/mobil",
    },
});

export const getCarDetailSEO = (car: {
    nama: string;
    merek: { nama: string };
    harga: number;
    deskripsi?: string;
}): Metadata => ({
    title: `${car.nama} - ${car.merek.nama} | Toko Jaya Motor`,
    description:
        car.deskripsi ||
        `${car.nama} bekas berkualitas dari ${
            car.merek.nama
        } dengan harga Rp ${car.harga.toLocaleString(
            "id-ID"
        )}. Hubungi kami untuk informasi lebih lanjut.`,
    keywords: `${car.nama}, ${car.merek.nama}, mobil bekas, jual mobil bekas`,
    openGraph: {
        title: `${car.nama} - ${car.merek.nama} | Toko Jaya Motor`,
        description:
            car.deskripsi ||
            `${car.nama} bekas berkualitas dari ${car.merek.nama} dengan harga terbaik.`,
        type: "website",
        images: [
            {
                url: "/images/car-placeholder.jpg",
                width: 1200,
                height: 630,
                alt: `${car.nama} - ${car.merek.nama}`,
            },
        ],
    },
    twitter: {
        card: "summary_large_image",
        title: `${car.nama} - ${car.merek.nama} | Toko Jaya Motor`,
        description:
            car.deskripsi ||
            `${car.nama} bekas berkualitas dari ${car.merek.nama} dengan harga terbaik.`,
        images: ["/images/car-placeholder.jpg"],
    },
});
