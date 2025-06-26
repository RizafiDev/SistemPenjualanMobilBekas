import { Card, CardContent, CardHeader } from "@/components/ui/card";
import { Skeleton } from "@/components/ui/skeleton";

export default function CatalogSkeleton() {
    return (
        <div className="container mx-auto px-4 py-8">
            {/* Header Skeleton */}
            <div className="mb-8">
                <Skeleton className="h-8 w-64 mb-2" />
                <Skeleton className="h-4 w-96" />
            </div>

            <div className="flex flex-col lg:flex-row gap-8">
                {/* Filters Sidebar Skeleton */}
                <div className="lg:w-1/4">
                    <Card>
                        <CardHeader>
                            <Skeleton className="h-6 w-16" />
                        </CardHeader>
                        <CardContent className="space-y-6">
                            {Array.from({ length: 7 }).map((_, i) => (
                                <div key={i}>
                                    <Skeleton className="h-4 w-20 mb-2" />
                                    <Skeleton className="h-10 w-full" />
                                </div>
                            ))}
                        </CardContent>
                    </Card>
                </div>

                {/* Main Content Skeleton */}
                <div className="lg:w-3/4">
                    {/* Sort and Results Info Skeleton */}
                    <div className="flex justify-between items-center mb-6">
                        <Skeleton className="h-4 w-32" />
                        <Skeleton className="h-10 w-48" />
                    </div>

                    {/* Cars Grid Skeleton */}
                    <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        {Array.from({ length: 9 }).map((_, i) => (
                            <Card key={i} className="animate-pulse">
                                <div className="h-48 bg-gray-200 rounded-t-lg"></div>
                                <CardContent className="p-4">
                                    <Skeleton className="h-4 w-full mb-2" />
                                    <Skeleton className="h-4 w-3/4 mb-2" />
                                    <Skeleton className="h-6 w-1/2 mb-3" />
                                    <div className="flex gap-2">
                                        <Skeleton className="h-6 w-16" />
                                        <Skeleton className="h-6 w-16" />
                                    </div>
                                </CardContent>
                            </Card>
                        ))}
                    </div>

                    {/* Pagination Skeleton */}
                    <div className="flex justify-center items-center mt-8 gap-2">
                        <Skeleton className="h-9 w-24" />
                        <div className="flex gap-1">
                            {Array.from({ length: 5 }).map((_, i) => (
                                <Skeleton key={i} className="h-9 w-9" />
                            ))}
                        </div>
                        <Skeleton className="h-9 w-24" />
                    </div>
                </div>
            </div>
        </div>
    );
}
