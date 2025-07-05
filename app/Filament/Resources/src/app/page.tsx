"use client";

import Link from "next/link";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import CarSearch from "@/components/car/car-search";
import StockCarCard from "@/components/car/stock-car-card";
import { useCatalog, useMereks, useLatestArticles } from "@/lib/hooks";
import type { StokMobil, Merek, Article } from "@/lib/types";
import {
  Car,
  Shield,
  Award,
  Users,
  ArrowRight,
  CheckCircle,
  Star,
  Clock,
  MapPin,
  Phone,
  Search,
} from "lucide-react";
import type { CarSearchFilters } from "@/lib/validations";
import { formatDistanceToNow } from "date-fns";
import { id } from "date-fns/locale";
import { useState } from "react";

function ArticlesOverview() {
  const { data: articlesData, isLoading, error } = useLatestArticles(6);

  const formatDate = (dateString: string) => {
    try {
      return formatDistanceToNow(new Date(dateString), {
        addSuffix: true,
        locale: id,
      });
    } catch {
      return "Tanggal tidak valid";
    }
  };

  if (isLoading) {
    return (
      <section className="py-16 bg-gray-50">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-3xl font-bold text-gray-900 mb-4">
              📰 Artikel & Tips Otomotif
            </h2>
            <div className="h-4 bg-gray-300 rounded mx-auto w-96 animate-pulse"></div>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {[...Array(6)].map((_, i) => (
              <div key={i} className="animate-pulse">
                <div className="bg-gray-300 h-48 rounded-lg mb-4"></div>
                <div className="h-4 bg-gray-300 rounded mb-2"></div>
                <div className="h-4 bg-gray-300 rounded w-3/4"></div>
              </div>
            ))}
          </div>
        </div>
      </section>
    );
  }

  if (error || !articlesData?.data?.length) {
    return null; // Don't show section if no articles
  }

  const articles = articlesData.data;

  return (
    <section className="py-16 bg-gray-50">
      <div className="container mx-auto px-4">
        {/* Section Header */}
        <div className="text-center mb-12">
          <h2 className="text-3xl font-bold text-gray-900 mb-4">
            📰 Artikel & Tips Otomotif
          </h2>
          <p className="text-lg text-gray-600 max-w-2xl mx-auto">
            Baca artikel terbaru seputar tips perawatan mobil, panduan pembelian,
            dan informasi otomotif lainnya untuk membantu Anda.
          </p>
        </div>

        {/* Articles Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          {articles.map((article: Article) => (
            <Link
              key={article.id}
              href={`/articles/${article.slug}`}
              className="group"
            >
              <div className="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                {/* Featured Image */}
                <div className="relative h-48 bg-gray-200">
                  {article.featured_image_url ? (
                    <Image
                      src={article.featured_image_url}
                      alt={article.title}
                      fill
                      className="object-cover group-hover:scale-105 transition-transform duration-300"
                    />
                  ) : (
                    <div className="flex items-center justify-center h-full text-gray-400">
                      <div className="text-center">
                        <div className="text-4xl mb-2">📰</div>
                        <p className="text-sm">Tidak ada gambar</p>
                      </div>
                    </div>
                  )}
                </div>

                {/* Content */}
                <div className="p-6">
                  <h3 className="text-lg font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors line-clamp-2">
                    {article.title}
                  </h3>

                  {article.excerpt && (
                    <p className="text-gray-600 text-sm mb-4 line-clamp-2">
                      {article.excerpt}
                    </p>
                  )}

                  <div className="flex items-center justify-between text-xs text-gray-500">
                    <span className="flex items-center gap-1">
                      🕒{" "}
                      {formatDate(article.published_at || article.created_at)}
                    </span>
                    <span className="text-blue-600 font-medium group-hover:underline">
                      Baca &rarr;
                    </span>
                  </div>
                </div>
              </div>
            </Link>
          ))}
        </div>

        {/* View All Articles Button */}
        <div className="text-center">
          <Link
            href="/articles"
            className="inline-flex items-center gap-2 px-8 py-3 bg-white border-2 border-blue-600 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-colors font-medium"
          >
            📚 Lihat Semua Artikel
            <span>→</span>
          </Link>
        </div>
      </div>
    </section>
  );
}

export default function HomePage() {
  const { data: stockCarsData } = useCatalog({ page: 1 }); // Ambil stok mobil yang tersedia
  const { data: mereksData } = useMereks();
  const featuredStockCars: StokMobil[] = stockCarsData?.data?.slice(0, 6) || [];
  const mereks: Merek[] = mereksData?.data || [];

  const handleSearch = (filters: CarSearchFilters) => {
    // Navigate to catalog page with filters
    const params = new URLSearchParams();
    Object.entries(filters).forEach(([key, value]) => {
      if (value !== undefined && value !== "") {
        params.set(key, value.toString());
      }
    });
    window.location.href = `/mobil?${params.toString()}`;
  };
  return (
    <div className="min-h-screen ">
      {" "}
      {/* Hero Section */}
      <section className="relative bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-20 lg:py-32 overflow-hidden">
        <div className="absolute inset-0 bg-white/60 backdrop-blur-sm"></div>
        <div className="container relative z-10 mx-auto px-4 lg:px-8">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div className="space-y-8">
              <div className="space-y-6">
                <Badge
                  variant="secondary"
                  className="w-fit px-4 py-2 bg-blue-100 text-blue-700 border-blue-200"
                >
                  🏆 Platform Terpercaya #1 di Indonesia
                </Badge>
                <h1 className="text-4xl lg:text-6xl font-bold text-gray-900 leading-tight">
                  Temukan
                  <span className="text-transparent bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text block">
                    Mobil Bekas
                  </span>
                  Impian Anda
                </h1>
                <p className="text-xl text-gray-600 leading-relaxed max-w-xl">
                  Ribuan pilihan mobil bekas berkualitas dengan harga terbaik.
                  Garansi resmi, pemeriksaan menyeluruh, dan layanan terpercaya.
                </p>{" "}
              </div>
              {/* Statistics */}
              <div className="grid grid-cols-3 gap-6">
                <div className="text-center p-4 bg-white/50 rounded-xl backdrop-blur-sm border border-white/20">
                  <div className="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                    1000+
                  </div>
                  <div className="text-sm text-gray-600 font-medium">
                    Mobil Tersedia
                  </div>
                </div>
                <div className="text-center p-4 bg-white/50 rounded-xl backdrop-blur-sm border border-white/20">
                  <div className="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                    50K+
                  </div>
                  <div className="text-sm text-gray-600 font-medium">
                    Pelanggan Puas
                  </div>
                </div>
                <div className="text-center p-4 bg-white/50 rounded-xl backdrop-blur-sm border border-white/20">
                  <div className="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                    98%
                  </div>
                  <div className="text-sm text-gray-600 font-medium">
                    Rating Kepuasan
                  </div>
                </div>
              </div>{" "}
              {/* CTA Buttons */}
              <div className="flex flex-col sm:flex-row gap-4">
                <Button
                  size="lg"
                  className="h-12 px-8 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 shadow-lg hover:shadow-xl transition-all duration-300"
                  asChild
                >
                  <Link href="/mobil">
                    Lihat Semua Mobil
                    <ArrowRight className="ml-2 h-5 w-5" />
                  </Link>
                </Button>
                <Button
                  size="lg"
                  variant="outline"
                  className="h-12 px-8 border-2 hover:bg-white hover:shadow-lg transition-all duration-300"
                  asChild
                >
                  <Link href="/janji-temu">
                    <Phone className="mr-2 h-5 w-5" />
                    Konsultasi Gratis
                  </Link>
                </Button>
              </div>
            </div>{" "}
            {/* Hero Image */}
            <div className="relative">
              <div className="aspect-[4/3] relative rounded-2xl overflow-hidden shadow-2xl bg-gradient-to-br from-gray-100 to-gray-200 border border-white/20">
                <div className="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100">
                  <div className="text-center space-y-4">
                    <Car className="h-24 w-24 text-blue-500 mx-auto" />
                    <p className="text-gray-600 font-medium">
                      Foto Mobil Unggulan
                    </p>
                  </div>
                </div>
              </div>

              {/* Floating Cards */}
              <div className="absolute -top-4 -left-4 bg-white rounded-xl shadow-xl p-4 max-w-[200px] border border-white/20 backdrop-blur-sm">
                <div className="flex items-center gap-3">
                  <div className="p-2 bg-green-100 rounded-full">
                    <CheckCircle className="h-4 w-4 text-green-600" />
                  </div>
                  <span className="text-sm font-semibold text-gray-800">
                    Garansi Resmi
                  </span>
                </div>
              </div>

              <div className="absolute -bottom-4 -right-4 bg-white rounded-xl shadow-xl p-4 max-w-[200px] border border-white/20 backdrop-blur-sm">
                <div className="flex items-center gap-3">
                  <div className="p-2 bg-yellow-100 rounded-full">
                    <Star className="h-4 w-4 text-yellow-600" />
                  </div>
                  <span className="text-sm font-semibold text-gray-800">
                    Rating 4.9/5
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>{" "}
      {/* Search Section */}
      <section className="py-20 bg-white relative overflow-hidden ">
        <div className="absolute inset-0 bg-gradient-to-b from-gray-50/50 to-white"></div>
        <div className="container relative z-10 mx-auto px-4 lg:px-8 container">
          <div className="text-center mb-12">
            <div className="inline-flex items-center gap-2 px-4 py-2 bg-blue-100 text-blue-700 rounded-full text-sm font-medium mb-4">
              <Search className="h-4 w-4" />
              Pencarian Mobil
            </div>
            <h2 className="text-3xl lg:text-4xl font-bold text-gray-900 mb-6">
              Cari Mobil Impian Anda
            </h2>
            <p className="text-lg text-gray-600 max-w-2xl mx-auto">
              Gunakan filter pencarian untuk menemukan mobil yang sesuai dengan
              kebutuhan dan budget Anda
            </p>
          </div>

          <CarSearch onSearch={handleSearch} showAdvancedFilters={true} />
        </div>
      </section>{" "}
      {/* Featured Cars */}
      <section className="py-20 bg-gradient-to-b from-gray-50 to-white ">
        <div className="container mx-auto px-4 lg:px-8 container">
          <div className="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-12 gap-6">
            <div className="space-y-4">
              <div className="inline-flex items-center gap-2 px-4 py-2 bg-purple-100 text-purple-700 rounded-full text-sm font-medium">
                <Car className="h-4 w-4" />
                Pilihan Terbaik
              </div>
              <h2 className="text-3xl lg:text-4xl font-bold text-gray-900">
                Mobil Pilihan Terbaik
              </h2>
              <p className="text-lg text-gray-600 max-w-xl">
                Koleksi mobil bekas berkualitas tinggi yang telah melewati
                inspeksi ketat
              </p>
            </div>
            <Button
              variant="outline"
              className="h-12 px-6 border-2 hover:shadow-lg transition-all duration-300"
              asChild
            >
              <Link href="/mobil">
                Lihat Semua
                <ArrowRight className="ml-2 h-4 w-4" />
              </Link>
            </Button>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {featuredStockCars.map((stockCar) => (
              <StockCarCard key={stockCar.id} stockCar={stockCar} />
            ))}
          </div>{" "}
          {featuredStockCars.length === 0 && (
            <div className="text-center py-16">
              <div className="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <Car className="h-12 w-12 text-gray-400" />
              </div>
              <h3 className="text-xl font-semibold text-gray-900 mb-3">
                Belum ada mobil tersedia
              </h3>
              <p className="text-gray-600 mb-8 max-w-md mx-auto">
                Kami sedang mempersiapkan koleksi mobil terbaik untuk Anda.
                Silakan cek kembali nanti atau hubungi kami untuk informasi
                lebih lanjut.
              </p>
              <Button className="h-12 px-8" asChild>
                <Link href="/mobil">Cek Katalog Lengkap</Link>
              </Button>
            </div>
          )}
        </div>
      </section>{" "}
      {/* Features Section */}
      <section className="py-20 bg-white ">
        <div className="container mx-auto px-4 lg:px-8 container">
          <div className="text-center mb-16">
            <div className="inline-flex items-center gap-2 px-4 py-2 bg-green-100 text-green-700 rounded-full text-sm font-medium mb-4">
              <Shield className="h-4 w-4" />
              Keunggulan Kami
            </div>
            <h2 className="text-3xl lg:text-4xl font-bold text-gray-900 mb-6">
              Mengapa Memilih Kami?
            </h2>
            <p className="text-lg text-gray-600 max-w-2xl mx-auto">
              Komitmen kami untuk memberikan layanan terbaik dalam jual beli
              mobil bekas
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <Card className="text-center hover:shadow-xl transition-all duration-300 border-0 shadow-lg bg-gradient-to-b from-white to-gray-50">
              <CardHeader className="pb-6">
                <div className="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl flex items-center justify-center mx-auto mb-6">
                  <Shield className="h-8 w-8 text-blue-600" />
                </div>
                <CardTitle className="text-xl">Garansi Resmi</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-gray-600 leading-relaxed">
                  Semua mobil dilengkapi garansi resmi dan jaminan kualitas
                  untuk memberikan ketenangan pikiran Anda.
                </p>
              </CardContent>
            </Card>

            <Card className="text-center hover:shadow-xl transition-all duration-300 border-0 shadow-lg bg-gradient-to-b from-white to-gray-50">
              <CardHeader className="pb-6">
                <div className="w-16 h-16 bg-gradient-to-br from-green-100 to-green-200 rounded-2xl flex items-center justify-center mx-auto mb-6">
                  <Award className="h-8 w-8 text-green-600" />
                </div>
                <CardTitle className="text-xl">Inspeksi Menyeluruh</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-gray-600 leading-relaxed">
                  Setiap mobil melalui inspeksi 100+ poin oleh teknisi
                  berpengalaman untuk memastikan kualitas terbaik.
                </p>
              </CardContent>
            </Card>

            <Card className="text-center hover:shadow-xl transition-all duration-300 border-0 shadow-lg bg-gradient-to-b from-white to-gray-50">
              <CardHeader className="pb-6">
                <div className="w-16 h-16 bg-gradient-to-br from-purple-100 to-purple-200 rounded-2xl flex items-center justify-center mx-auto mb-6">
                  <Users className="h-8 w-8 text-purple-600" />
                </div>
                <CardTitle className="text-xl">Layanan Terpercaya</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-gray-600 leading-relaxed">
                  Tim profesional siap membantu Anda dari konsultasi hingga
                  proses transaksi selesai.
                </p>
              </CardContent>
            </Card>

            <Card className="text-center hover:shadow-xl transition-all duration-300 border-0 shadow-lg bg-gradient-to-b from-white to-gray-50">
              <CardHeader className="pb-6">
                <div className="w-16 h-16 bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-2xl flex items-center justify-center mx-auto mb-6">
                  <Clock className="h-8 w-8 text-yellow-600" />
                </div>
                <CardTitle className="text-xl">Proses Cepat</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-gray-600 leading-relaxed">
                  Proses pembelian yang efisien dengan bantuan digitalisasi
                  dokumen dan sistem terintegrasi.
                </p>
              </CardContent>
            </Card>

            <Card className="text-center hover:shadow-xl transition-all duration-300 border-0 shadow-lg bg-gradient-to-b from-white to-gray-50">
              <CardHeader className="pb-6">
                <div className="w-16 h-16 bg-gradient-to-br from-red-100 to-red-200 rounded-2xl flex items-center justify-center mx-auto mb-6">
                  <MapPin className="h-8 w-8 text-red-600" />
                </div>
                <CardTitle className="text-xl">Lokasi Strategis</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-gray-600 leading-relaxed">
                  Showroom di berbagai kota dengan akses mudah dan fasilitas
                  lengkap untuk kenyamanan Anda.
                </p>
              </CardContent>
            </Card>

            <Card className="text-center hover:shadow-xl transition-all duration-300 border-0 shadow-lg bg-gradient-to-b from-white to-gray-50">
              <CardHeader className="pb-6">
                <div className="w-16 h-16 bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-2xl flex items-center justify-center mx-auto mb-6">
                  <Car className="h-8 w-8 text-indigo-600" />
                </div>
                <CardTitle className="text-xl">Pilihan Lengkap</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-gray-600 leading-relaxed">
                  Ribuan pilihan mobil dari berbagai merek dan tahun dengan
                  kondisi prima dan harga kompetitif.
                </p>
              </CardContent>
            </Card>
          </div>
        </div>
      </section>{" "}
      {/* Brands Section */}
      {mereks.length > 0 && (
        <section className="py-20 bg-gradient-to-b from-gray-50 to-white ">
          <div className="mx-auto px-4 lg:px-8 container">
            <div className="text-center mb-16">
              <div className="inline-flex items-center gap-2 px-4 py-2 bg-orange-100 text-orange-700 rounded-full text-sm font-medium mb-4">
                <Car className="h-4 w-4" />
                Merek Terpercaya
              </div>
              <h2 className="text-3xl lg:text-4xl font-bold text-gray-900 mb-6">
                Merek Populer
              </h2>
              <p className="text-lg text-gray-600 max-w-2xl mx-auto">
                Temukan mobil dari merek-merek terpercaya pilihan Anda dengan
                kualitas terjamin
              </p>
            </div>
            <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
              {mereks.slice(0, 12).map((merek) => (
                <Link
                  key={merek.id}
                  href={`/mobil?merek=${merek.id}`}
                  className="group block"
                >
                  <Card className="text-center hover:shadow-xl transition-all duration-300 border-0 shadow-md bg-white hover:scale-105">
                    <CardContent className="p-8">
                      <div className="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:from-blue-100 group-hover:to-blue-200 transition-all duration-300">
                        <Car className="h-8 w-8 text-gray-600 group-hover:text-blue-600 transition-colors duration-300" />
                      </div>
                      <h3 className="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors duration-300">
                        {merek.nama}
                      </h3>
                    </CardContent>
                  </Card>
                </Link>
              ))}
            </div>
            <div className="text-center mt-12">
              <Button
                variant="outline"
                className="h-12 px-8 border-2 hover:shadow-lg transition-all duration-300"
                asChild
              >
                <Link href="/mobil">
                  Lihat Semua Merek
                  <ArrowRight className="ml-2 h-4 w-4" />
                </Link>
              </Button>
            </div>
          </div>
        </section>
      )}{" "}
      {/* CTA Section */}
      <section className="py-20 bg-gradient-to-br from-blue-600 via-blue-700 to-purple-700 relative overflow-hidden">
        <div className="absolute inset-0 bg-gradient-to-r from-black/20 to-transparent"></div>
        <div className="container text-center relative z-10 mx-auto px-4 lg:px-8">
          <div className="max-w-4xl mx-auto">
            <div className="inline-flex items-center gap-2 px-4 py-2 bg-white/20 text-white rounded-full text-sm font-medium mb-6 backdrop-blur-sm">
              <Phone className="h-4 w-4" />
              Siap Melayani Anda
            </div>
            <h2 className="text-3xl lg:text-5xl font-bold text-white mb-8 leading-tight">
              Siap Menemukan Mobil Impian Anda?
            </h2>
            <p className="text-xl text-blue-100 mb-10 leading-relaxed max-w-3xl mx-auto">
              Jangan tunggu lagi! Tim ahli kami siap membantu Anda menemukan
              mobil bekas berkualitas yang sesuai dengan kebutuhan dan budget.
            </p>
            <div className="flex flex-col sm:flex-row gap-6 justify-center">
              <Button
                size="lg"
                variant="secondary"
                className="h-14 px-10 text-lg font-semibold shadow-xl hover:shadow-2xl transition-all duration-300"
                asChild
              >
                <Link href="/mobil">
                  <Car className="mr-3 h-6 w-6" />
                  Jelajahi Katalog
                </Link>
              </Button>
              <Button
                size="lg"
                variant="outline"
                className="h-14 px-10 text-lg font-semibold text-white border-2 border-white hover:bg-white hover:text-blue-700 shadow-xl hover:shadow-2xl transition-all duration-300"
                asChild
              >
                <Link href="/janji-temu">
                  <Phone className="mr-3 h-6 w-6" />
                  Hubungi Kami
                </Link>
              </Button>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}
