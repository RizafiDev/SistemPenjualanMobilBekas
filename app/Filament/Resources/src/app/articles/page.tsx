"use client";

import { useState } from "react";
import { useArticles } from "@/lib/hooks";
import { Article } from "@/lib/types";
import Link from "next/link";
import Image from "next/image";
import { formatDistanceToNow } from "date-fns";
import { id } from "date-fns/locale";

export default function ArticlesPage() {
    const [currentPage, setCurrentPage] = useState(1);
    const [searchQuery, setSearchQuery] = useState("");
    const [sortBy, setSortBy] = useState("-published_at");

    const { data, error, isLoading } = useArticles({
        page: currentPage,
        search: searchQuery,
        status: "published",
        sortBy: sortBy,
        per_page: 12,
    });

    const handleSearch = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        setCurrentPage(1);
    };

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
            <div className="container mx-auto px-4 py-8">
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
        );
    }

    if (error) {
        return (
            <div className="container mx-auto px-4 py-8">
                <div className="text-center">
                    <h2 className="text-2xl font-bold text-gray-900 mb-4">
                        Terjadi Kesalahan
                    </h2>
                    <p className="text-gray-600">
                        Gagal memuat artikel. Silakan coba lagi nanti.
                    </p>
                </div>
            </div>
        );
    }

    const articles = data?.data || [];

    return (
        <div className="min-h-screen bg-gray-50">
            {/* Header */}
            <div className="bg-white shadow-sm">
                <div className="container mx-auto px-4 py-8">
                    <div className="text-center mb-8">
                        <h1 className="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                            📰 Artikel & Tips Otomotif
                        </h1>
                        <p className="text-lg text-gray-600 max-w-2xl mx-auto">
                            Temukan informasi terbaru, tips, dan panduan seputar
                            dunia otomotif untuk membantu Anda membuat keputusan
                            terbaik.
                        </p>
                    </div>

                    {/* Search and Filter */}
                    <div className="flex flex-col md:flex-row gap-4 justify-between items-center">
                        <form onSubmit={handleSearch} className="flex gap-2">
                            <input
                                type="text"
                                placeholder="Cari artikel..."
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                                className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                            <button
                                type="submit"
                                className="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                            >
                                🔍 Cari
                            </button>
                        </form>

                        <select
                            value={sortBy}
                            onChange={(e) => setSortBy(e.target.value)}
                            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="-published_at">Terbaru</option>
                            <option value="published_at">Terlama</option>
                            <option value="title">A-Z</option>
                            <option value="-title">Z-A</option>
                        </select>
                    </div>
                </div>
            </div>

            {/* Articles Grid */}
            <div className="container mx-auto px-4 py-8">
                {articles.length === 0 ? (
                    <div className="text-center py-12">
                        <div className="text-6xl mb-4">📝</div>
                        <h3 className="text-xl font-semibold text-gray-900 mb-2">
                            Tidak Ada Artikel
                        </h3>
                        <p className="text-gray-600">
                            {searchQuery
                                ? "Tidak ditemukan artikel yang sesuai dengan pencarian Anda."
                                : "Belum ada artikel yang dipublikasikan."}
                        </p>
                    </div>
                ) : (
                    <>
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                            {articles.map((article: Article) => (
                                <ArticleCard
                                    key={article.id}
                                    article={article}
                                    formatDate={formatDate}
                                />
                            ))}
                        </div>

                        {/* Pagination */}
                        {data && data.last_page > 1 && (
                            <div className="flex justify-center gap-2">
                                <button
                                    onClick={() =>
                                        setCurrentPage(Math.max(1, currentPage - 1))
                                    }
                                    disabled={currentPage === 1}
                                    className="px-4 py-2 border border-gray-300 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
                                >
                                    ← Sebelumnya
                                </button>
                                
                                <span className="px-4 py-2 text-gray-600">
                                    Halaman {currentPage} dari {data.last_page}
                                </span>
                                
                                <button
                                    onClick={() =>
                                        setCurrentPage(
                                            Math.min(data.last_page, currentPage + 1)
                                        )
                                    }
                                    disabled={currentPage === data.last_page}
                                    className="px-4 py-2 border border-gray-300 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
                                >
                                    Selanjutnya →
                                </button>
                            </div>
                        )}
                    </>
                )}
            </div>
        </div>
    );
}

// Article Card Component
function ArticleCard({
    article,
    formatDate,
}: {
    article: Article;
    formatDate: (date: string) => string;
}) {
    return (
        <Link href={`/articles/${article.slug}`}>
            <div className="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden group">
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
                    <h3 className="text-xl font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors line-clamp-2">
                        {article.title}
                    </h3>
                    
                    {article.excerpt && (
                        <p className="text-gray-600 text-sm mb-4 line-clamp-3">
                            {article.excerpt}
                        </p>
                    )}
                    
                    <div className="flex items-center justify-between text-xs text-gray-500">
                        <span className="flex items-center gap-1">
                            🕒 {formatDate(article.published_at || article.created_at)}
                        </span>
                        <span className="text-blue-600 font-medium group-hover:underline">
                            Baca Selengkapnya →
                        </span>
                    </div>
                </div>
            </div>
        </Link>
    );
}
