"use client";

import { useArticle, usePopularArticles } from "@/lib/hooks";
import { Article } from "@/lib/types";
import Link from "next/link";
import Image from "next/image";
import { formatDistanceToNow } from "date-fns";
import { id } from "date-fns/locale";
import { useParams } from "next/navigation";

export default function ArticleDetailPage() {
    const params = useParams();
    const slug = params.slug as string;
    
    const { data: article, error, isLoading } = useArticle(slug);
    const { data: popularArticles } = usePopularArticles(5);

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
            <div className="min-h-screen bg-gray-50">
                <div className="container mx-auto px-4 py-8">
                    <div className="animate-pulse">
                        <div className="h-8 bg-gray-300 rounded mb-4 w-3/4"></div>
                        <div className="h-64 bg-gray-300 rounded mb-6"></div>
                        <div className="space-y-4">
                            {[...Array(5)].map((_, i) => (
                                <div key={i} className="h-4 bg-gray-300 rounded"></div>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        );
    }

    if (error || !article) {
        return (
            <div className="min-h-screen bg-gray-50 flex items-center justify-center">
                <div className="text-center">
                    <div className="text-6xl mb-4">❌</div>
                    <h2 className="text-2xl font-bold text-gray-900 mb-4">
                        Artikel Tidak Ditemukan
                    </h2>
                    <p className="text-gray-600 mb-6">
                        Artikel yang Anda cari tidak ditemukan atau mungkin telah dihapus.
                    </p>
                    <Link
                        href="/articles"
                        className="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        ← Kembali ke Artikel
                    </Link>
                </div>
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-gray-50">
            {/* Breadcrumb */}
            <div className="bg-white shadow-sm">
                <div className="container mx-auto px-4 py-4">
                    <nav className="text-sm text-gray-600">
                        <Link href="/" className="hover:text-blue-600">
                            Beranda
                        </Link>
                        <span className="mx-2">›</span>
                        <Link href="/articles" className="hover:text-blue-600">
                            Artikel
                        </Link>
                        <span className="mx-2">›</span>
                        <span className="text-gray-900">{article.title}</span>
                    </nav>
                </div>
            </div>

            <div className="container mx-auto px-4 py-8">
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {/* Main Content */}
                    <div className="lg:col-span-2">
                        <article className="bg-white rounded-lg shadow-sm overflow-hidden">
                            {/* Featured Image */}
                            {article.featured_image_url && (
                                <div className="relative h-64 md:h-96">
                                    <Image
                                        src={article.featured_image_url}
                                        alt={article.title}
                                        fill
                                        className="object-cover"
                                    />
                                </div>
                            )}

                            {/* Article Content */}
                            <div className="p-6 md:p-8">
                                {/* Header */}
                                <header className="mb-6">
                                    <h1 className="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
                                        {article.title}
                                    </h1>
                                    
                                    <div className="flex items-center gap-4 text-sm text-gray-600">
                                        <span className="flex items-center gap-1">
                                            🕒 {formatDate(article.published_at || article.created_at)}
                                        </span>
                                        <span className="flex items-center gap-1">
                                            📖 {Math.ceil(article.content.length / 1000)} menit baca
                                        </span>
                                    </div>
                                </header>

                                {/* Excerpt */}
                                {article.excerpt && (
                                    <div className="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                                        <p className="text-blue-800 italic">
                                            {article.excerpt}
                                        </p>
                                    </div>
                                )}

                                {/* Content */}
                                <div 
                                    className="prose prose-lg max-w-none"
                                    dangerouslySetInnerHTML={{ __html: article.content }}
                                />

                                {/* Share Buttons */}
                                <div className="mt-8 pt-6 border-t border-gray-200">
                                    <h3 className="text-lg font-semibold text-gray-900 mb-4">
                                        Bagikan Artikel Ini
                                    </h3>
                                    <div className="flex gap-3">
                                        <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                            📘 Facebook
                                        </button>
                                        <button className="px-4 py-2 bg-blue-400 text-white rounded-lg hover:bg-blue-500 transition-colors">
                                            🐦 Twitter
                                        </button>
                                        <button className="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                            📱 WhatsApp
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>

                    {/* Sidebar */}
                    <div className="lg:col-span-1">
                        <div className="sticky top-8 space-y-6">
                            {/* Popular Articles */}
                            {popularArticles && popularArticles.data.length > 0 && (
                                <div className="bg-white rounded-lg shadow-sm p-6">
                                    <h3 className="text-lg font-semibold text-gray-900 mb-4">
                                        📈 Artikel Populer
                                    </h3>
                                    <div className="space-y-4">
                                        {popularArticles.data.slice(0, 5).map((popularArticle: Article) => (
                                            <Link
                                                key={popularArticle.id}
                                                href={`/articles/${popularArticle.slug}`}
                                                className="block group"
                                            >
                                                <div className="flex gap-3">
                                                    <div className="relative w-16 h-16 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                                        {popularArticle.featured_image_url ? (
                                                            <Image
                                                                src={popularArticle.featured_image_url}
                                                                alt={popularArticle.title}
                                                                fill
                                                                className="object-cover"
                                                            />
                                                        ) : (
                                                            <div className="flex items-center justify-center h-full text-gray-400 text-xs">
                                                                📰
                                                            </div>
                                                        )}
                                                    </div>
                                                    <div>
                                                        <h4 className="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-2">
                                                            {popularArticle.title}
                                                        </h4>
                                                        <p className="text-xs text-gray-500 mt-1">
                                                            {formatDate(popularArticle.published_at || popularArticle.created_at)}
                                                        </p>
                                                    </div>
                                                </div>
                                            </Link>
                                        ))}
                                    </div>
                                </div>
                            )}

                            {/* Back to Articles */}
                            <div className="bg-white rounded-lg shadow-sm p-6">
                                <Link
                                    href="/articles"
                                    className="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium"
                                >
                                    ← Lihat Semua Artikel
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
