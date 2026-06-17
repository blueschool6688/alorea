export const generateProductSEO = (product) => {
    if (!product) return null;

    const baseTitle = product.meta_title || product.name || 'Sản phẩm';
    const category = product.category || 'Nước hoa';

    return {
        title: `${baseTitle} - ${category} Chính Hãng | ALORÉNA`,
        description: product.meta_description || product.short_description,
        keywords: product.meta_keywords ||
            `${baseTitle}, ${category}, nước hoa, perfume, ${product.fragrance?.top?.join(', ') || ''}, chính hãng, ALORÉNA`,
        image: product.main_image_url || product.gallery_urls?.[0],
        url: window.location.href,
        type: 'product',
        product: {
            name: product.name,
            description: product.short_description || product.description,
            price: product.price,
            image: product.main_image_url,
            images: product.gallery_urls,
            stock: product.stock,
            sku: product.sku,
            category: product.category,
            rating: product.rating,
            totalReviews: product.review_count
        }
    };
};

export const generateCategorySEO = (category, products = []) => {
    return {
        title: `${category} - Nước Hoa Chính Hãng | ALORÉNA`,
        description: `Khám phá bộ sưu tập ${category} chính hãng tại ALORÉNA. ${products.length} sản phẩm cao cấp với mùi hương độc đáo và chất lượng tuyệt vời.`,
        keywords: `${category}, nước hoa, perfume, chính hãng, ALORÉNA, bộ sưu tập`,
        image: products[0]?.image,
        url: window.location.href,
        type: 'website'
    };
};

export const generatePageSEO = ({ title, description, keywords, image, type = 'website' }) => {
    return {
        title: title || 'ALORÉNA - Nước Hoa Chính Hãng Cao Cấp',
        description: description || 'ALORÉNA - Điểm đến lý tưởng cho những tín đồ nước hoa. Bộ sưu tập nước hoa chính hãng cao cấp từ các thương hiệu nổi tiếng thế giới.',
        keywords: keywords || 'nước hoa, perfume, chính hãng, cao cấp, ALORÉNA',
        image: image || '/images/default-og-image.jpg',
        url: window.location.href,
        type
    };
};

export const generateHomepageWithDataSEO = (homeData) => {
    const { flashSales = [], productCategory = [], collections = [] } = homeData || {};

    const brandKeywords = productCategory
        .map(product => product.brand)
        .filter(Boolean)
        .slice(0, 5)
        .join(', ');

    const categoryKeywords = collections
        .map(cat => cat.name)
        .slice(0, 5)
        .join(', ');

    const flashSaleKeywords = flashSales
        .map(sale => `${sale.name}, giảm giá ${sale.discount_percentage}%`)
        .slice(0, 3)
        .join(', ');

    return {
        title: `ALORÉNA - Nước Hoa Chính Hãng ${new Date().getFullYear()} | ${productCategory.length}+ Sản Phẩm Cao Cấp`,
        description: `Khám phá ${productCategory.length}+ sản phẩm nước hoa chính hãng tại ALORÉNA. Flash Sale ${flashSales.length > 0 ? `giảm đến ${Math.max(...flashSales.map(s => s.discount_percentage))}%` : 'hấp dẫn'}`,
        keywords: `nước hoa chính hãng, ALORÉNA, ${categoryKeywords}, ${flashSaleKeywords}, perfume Vietnam, nước hoa ${new Date().getFullYear()}`,
        image: productCategory[0]?.image_url || '/images/homepage-banner.jpg',
        url: window.location.href,
        type: 'website',
        organizationData: {
            "@context": "https://schema.org",
            "@type": "Organization",
            "name": "ALORÉNA",
            "description": `Cửa hàng nước hoa chính hãng với ${productCategory.length}+ sản phẩm cao cấp`,
            "url": window.location.origin,
            "logo": `${window.location.origin}/images/logo.png`,
            "contactPoint": {
                "@type": "ContactPoint",
                "telephone": "+84-xxx-xxx-xxx",
                "contactType": "customer service",
                "availableLanguage": "Vietnamese"
            },
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "123 Đường ABC",
                "addressLocality": "Hồ Chí Minh",
                "addressCountry": "VN"
            }
        },
        productCategory: productCategory.slice(0, 5).map(product => ({
            "@type": "Product",
            "name": product.name,
            "description": product.description,
            "image": product.image,
            "offers": {
                "@type": "Offer",
                "price": product.price,
                "priceCurrency": "VND",
                "availability": product.stock > 0 ? "https://schema.org/InStock" : "https://schema.org/OutOfStock"
            }
        }))
    };
};

export const generateBlogDetailSEO = (blog, relatedBlogs = []) => {
    if (!blog) return {};

    const publishDate = new Date(blog.created_at).toISOString();
    const modifiedDate = new Date(blog.updated_at || blog.created_at).toISOString();

    // Extract keywords from content and tags
    const tagKeywords = blog.tags?.map(tag => tag.name).join(', ') || '';
    const categoryKeyword = blog.category?.name || '';

    // Calculate reading time if not provided
    const wordCount = blog.content?.replace(/<[^>]*>/g, '').split(' ').length || 0;
    const readingTime = blog.reading_time || Math.ceil(wordCount / 200);

    return {
        title: `${blog.meta_title || blog.title} | ALORÉNA Blog - Nước Hoa & Làm Đẹp`,
        description: blog.excerpt || blog.meta_description ||
            `${blog.content?.replace(/<[^>]*>/g, '').substring(0, 155)}...`,
        keywords: `${blog.title}, ${categoryKeyword}, ${tagKeywords}, nước hoa, làm đẹp, beauty tips, perfume blog, ALORÉNA`,
        image: blog.featured_image_url || '/images/blog-default.jpg',
        url: window.location.href,
        type: 'article',

        // Open Graph
        'og:title': blog.title,
        'og:description': blog.excerpt || blog.meta_description,
        'og:image': blog.featured_image_url,
        'og:url': window.location.href,
        'og:type': 'article',
        'og:site_name': 'ALORÉNA',
        'og:locale': 'vi_VN',

        // Twitter Card
        'twitter:card': 'summary_large_image',
        'twitter:title': blog.title,
        'twitter:description': blog.excerpt || blog.meta_description,
        'twitter:image': blog.featured_image_url,

        // Article specific
        'article:published_time': publishDate,
        'article:modified_time': modifiedDate,
        'article:author': blog.author_name || 'ALORÉNA',
        'article:section': blog.category?.name || 'Beauty',
        'article:tag': blog.tags?.map(tag => tag.name).join(',') || '',

        // Schema.org structured data
        structuredData: {
            "@context": "https://schema.org",
            "@type": "BlogPosting",
            "headline": blog.title,
            "description": blog.excerpt || blog.meta_description,
            "image": {
                "@type": "ImageObject",
                "url": blog.featured_image_url,
                "width": 1200,
                "height": 630
            },
            "author": {
                "@type": "Person",
                "name": blog.author?.name || "ALORÉNA",
                "image": blog.author?.avatar,
                "description": blog.author?.bio
            },
            "publisher": {
                "@type": "Organization",
                "name": "ALORÉNA",
                "logo": {
                    "@type": "ImageObject",
                    "url": `${window.location.origin}/images/logo.png`
                }
            },
            "datePublished": publishDate,
            "dateModified": modifiedDate,
            "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": window.location.href
            },
            "articleSection": blog.category?.name || "Beauty",
            "keywords": blog.tags?.map(tag => tag.name).join(', '),
            "wordCount": wordCount,
            "timeRequired": `PT${readingTime}M`,
            "inLanguage": "vi-VN",
            "isPartOf": {
                "@type": "Blog",
                "@id": `${window.location.origin}/blogs`,
                "name": "ALORÉNA Blog"
            },
            "about": [
                {
                    "@type": "Thing",
                    "name": "Nước hoa"
                },
                {
                    "@type": "Thing",
                    "name": "Làm đẹp"
                }
            ]
        },

        // Breadcrumb structured data
        breadcrumbStructuredData: {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {
                    "@type": "ListItem",
                    "position": 1,
                    "name": "Trang chủ",
                    "item": window.location.origin
                },
                {
                    "@type": "ListItem",
                    "position": 2,
                    "name": "Blog",
                    "item": `${window.location.origin}/blogs`
                },
                {
                    "@type": "ListItem",
                    "position": 3,
                    "name": blog.title,
                    "item": window.location.href
                }
            ]
        },

        // Related articles (for SEO internal linking)
        relatedArticles: relatedBlogs.slice(0, 5).map(relatedBlog => ({
            "@type": "Article",
            "headline": relatedBlog.title,
            "url": `${window.location.origin}/blog/${relatedBlog.slug}`,
            "image": relatedBlog.featured_image_url,
            "datePublished": new Date(relatedBlog.created_at).toISOString()
        }))
    };
};

// Helper function to apply SEO to document
export const applySEOToDocument = (seoData) => {
    if (!seoData) return;

    // Update title
    if (seoData.title) {
        document.title = seoData.title;
    }

    // Update or create meta tags
    const updateMetaTag = (name, content, property = false) => {
        if (!content) return;

        const selector = property ? `meta[property="${name}"]` : `meta[name="${name}"]`;
        let tag = document.querySelector(selector);

        if (!tag) {
            tag = document.createElement('meta');
            if (property) {
                tag.setAttribute('property', name);
            } else {
                tag.setAttribute('name', name);
            }
            document.head.appendChild(tag);
        }
        tag.setAttribute('content', content);
    };

    // Basic meta tags
    updateMetaTag('description', seoData.description);
    updateMetaTag('keywords', seoData.keywords);

    // Open Graph tags
    updateMetaTag('og:title', seoData['og:title'], true);
    updateMetaTag('og:description', seoData['og:description'], true);
    updateMetaTag('og:image', seoData['og:image'], true);
    updateMetaTag('og:url', seoData['og:url'], true);
    updateMetaTag('og:type', seoData['og:type'], true);
    updateMetaTag('og:site_name', seoData['og:site_name'], true);
    updateMetaTag('og:locale', seoData['og:locale'], true);

    // Twitter Card tags
    updateMetaTag('twitter:card', seoData['twitter:card']);
    updateMetaTag('twitter:title', seoData['twitter:title']);
    updateMetaTag('twitter:description', seoData['twitter:description']);
    updateMetaTag('twitter:image', seoData['twitter:image']);

    // Article tags
    updateMetaTag('article:published_time', seoData['article:published_time'], true);
    updateMetaTag('article:modified_time', seoData['article:modified_time'], true);
    updateMetaTag('article:author', seoData['article:author'], true);
    updateMetaTag('article:section', seoData['article:section'], true);
    updateMetaTag('article:tag', seoData['article:tag'], true);

    // Structured data
    if (seoData.structuredData) {
        let structuredDataScript = document.querySelector('script[type="application/ld+json"][data-type="article"]');
        if (!structuredDataScript) {
            structuredDataScript = document.createElement('script');
            structuredDataScript.type = 'application/ld+json';
            structuredDataScript.setAttribute('data-type', 'article');
            document.head.appendChild(structuredDataScript);
        }
        structuredDataScript.textContent = JSON.stringify(seoData.structuredData);
    }

    // Breadcrumb structured data
    if (seoData.breadcrumbStructuredData) {
        let breadcrumbScript = document.querySelector('script[type="application/ld+json"][data-type="breadcrumb"]');
        if (!breadcrumbScript) {
            breadcrumbScript = document.createElement('script');
            breadcrumbScript.type = 'application/ld+json';
            breadcrumbScript.setAttribute('data-type', 'breadcrumb');
            document.head.appendChild(breadcrumbScript);
        }
        breadcrumbScript.textContent = JSON.stringify(seoData.breadcrumbStructuredData);
    }

    // Canonical URL
    let canonicalLink = document.querySelector('link[rel="canonical"]');
    if (!canonicalLink) {
        canonicalLink = document.createElement('link');
        canonicalLink.rel = 'canonical';
        document.head.appendChild(canonicalLink);
    }
    canonicalLink.href = seoData.url;
};

const formatPrice = (price) => {
    if (!price) return '0₫';
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(price);
};

export const generateIntroduceSEO = (introduceData) => {
    const { brandStory, signatureCollections, coreValues, statistics } = introduceData || {};

    const collectionNames = signatureCollections?.map(c => c.name).join(', ') || '';
    const valueNames = coreValues?.map(v => v.title).join(', ') || '';

    return {
        title: `Về ALORÉA - Thương Hiệu Nước Hoa Cao Cấp | Phong Cách Sống Đẳng Cấp`,
        description: `Khám phá câu chuyện thương hiệu ALORÉA - nước hoa quốc tế với dấu ấn Á Đông. 6 bộ sưu tập đặc trưng: ${collectionNames}. Chất lượng cao cấp, thiết kế sang trọng.`,
        keywords: `ALORÉA, thương hiệu nước hoa, ${collectionNames}, nước hoa cao cấp, perfume brand, ${valueNames}, câu chuyện thương hiệu`,
        image: '/images/alorena-brand-story.jpg',
        url: `${window.location.origin}/introduce`,
        type: 'website',

        // Open Graph
        'og:title': 'Về Thương Hiệu ALORÉA - Nước Hoa Đẳng Cấp Quốc Tế',
        'og:description': brandStory?.mission || 'ALORÉA - Thương hiệu nước hoa cao cấp với 6 bộ sưu tập đặc trưng',
        'og:image': '/images/alorena-brand-hero.jpg',
        'og:url': `${window.location.origin}/introduce`,
        'og:type': 'website',
        'og:site_name': 'ALORÉA',

        // Twitter Card
        'twitter:card': 'summary_large_image',
        'twitter:title': 'Về Thương Hiệu ALORÉA - Nước Hoa Đẳng Cấp',
        'twitter:description': brandStory?.tagline || 'ALORÉA không chỉ là nước hoa – ALORÉA là phong cách sống',
        'twitter:image': '/images/alorena-brand-hero.jpg',

        // Schema.org structured data
        structuredData: {
            "@context": "https://schema.org",
            "@type": "AboutPage",
            "name": "Về Thương Hiệu ALORÉA",
            "description": brandStory?.mission,
            "url": `${window.location.origin}/introduce`,
            "mainEntity": {
                "@type": "Organization",
                "name": "ALORÉA",
                "description": brandStory?.mission,
                "foundingDate": "2020",
                "url": window.location.origin,
                "logo": `${window.location.origin}/images/logo.png`,
                "slogan": brandStory?.tagline,
                "makesOffer": signatureCollections?.map(collection => ({
                    "@type": "Product",
                    "name": collection.name,
                    "description": collection.description,
                    "category": "Nước hoa"
                })),
                "hasOfferCatalog": {
                    "@type": "OfferCatalog",
                    "name": "Bộ Sưu Tập ALORÉA",
                    "itemListElement": signatureCollections?.map((collection, index) => ({
                        "@type": "OfferCatalog",
                        "position": index + 1,
                        "name": collection.name,
                        "description": collection.description
                    }))
                },
                "values": coreValues?.map(value => value.title).join(', '),
                "address": {
                    "@type": "PostalAddress",
                    "addressCountry": "VN",
                    "addressLocality": "Hồ Chí Minh"
                },
                "aggregateRating": {
                    "@type": "AggregateRating",
                    "ratingValue": "4.8",
                    "reviewCount": "1250",
                    "bestRating": "5",
                    "worstRating": "1"
                }
            }
        },

        // Breadcrumb
        breadcrumbStructuredData: {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {
                    "@type": "ListItem",
                    "position": 1,
                    "name": "Trang chủ",
                    "item": window.location.origin
                },
                {
                    "@type": "ListItem",
                    "position": 2,
                    "name": "Về chúng tôi",
                    "item": `${window.location.origin}/introduce`
                }
            ]
        },

        // Additional structured data for collections
        collectionsStructuredData: {
            "@context": "https://schema.org",
            "@type": "ItemList",
            "name": "Bộ Sưu Tập ALORÉA",
            "description": "6 bộ sưu tập nước hoa đặc trưng của thương hiệu ALORÉA",
            "numberOfItems": signatureCollections?.length || 6,
            "itemListElement": signatureCollections?.map((collection, index) => ({
                "@type": "Product",
                "position": index + 1,
                "name": collection.name,
                "description": collection.description,
                "category": "Nước hoa",
                "brand": {
                    "@type": "Brand",
                    "name": "ALORÉA"
                },
                "image": collection.image
            })) || []
        }
    };
};
