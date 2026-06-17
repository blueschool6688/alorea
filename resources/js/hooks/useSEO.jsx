import { useEffect } from 'react';

const useSEO = (seoData) => {
    useEffect(() => {
        if (!seoData) return;
        if (seoData.title) {
            document.title = seoData.title;
        }
        const updateMetaTag = (name, content, property = false) => {
            if (!content) return;

            const selector = property ? `meta[property="${name}"]` : `meta[name="${name}"]`;
            let meta = document.querySelector(selector);

            if (!meta) {
                meta = document.createElement('meta');
                if (property) {
                    meta.setAttribute('property', name);
                } else {
                    meta.setAttribute('name', name);
                }
                document.head.appendChild(meta);
            }

            meta.setAttribute('content', content);
        };

        // Update basic meta tags
        updateMetaTag('description', seoData.description);
        updateMetaTag('keywords', seoData.keywords);

        // Update Open Graph tags
        updateMetaTag('og:title', seoData.title, true);
        updateMetaTag('og:description', seoData.description, true);
        updateMetaTag('og:image', seoData.image, true);
        updateMetaTag('og:url', seoData.url || window.location.href, true);
        updateMetaTag('og:type', seoData.type || 'website', true);
        updateMetaTag('og:site_name', 'ALORÉNA', true);

        // Update Twitter Card tags
        updateMetaTag('twitter:card', 'summary_large_image');
        updateMetaTag('twitter:title', seoData.title);
        updateMetaTag('twitter:description', seoData.description);
        updateMetaTag('twitter:image', seoData.image);

        // Add canonical URL
        let canonical = document.querySelector('link[rel="canonical"]');
        if (!canonical) {
            canonical = document.createElement('link');
            canonical.setAttribute('rel', 'canonical');
            document.head.appendChild(canonical);
        }
        canonical.setAttribute('href', seoData.url || window.location.href);

        // Remove existing structured data
        const existingScripts = document.querySelectorAll('script[type="application/ld+json"]');
        existingScripts.forEach(script => script.remove());

        if (seoData.organizationData) {
            const orgScript = document.createElement('script');
            orgScript.type = 'application/ld+json';
            orgScript.textContent = JSON.stringify(seoData.organizationData);
            document.head.appendChild(orgScript);
        }
        if (seoData.productCategory && seoData.productCategory.length > 0) {
            const productsScript = document.createElement('script');
            productsScript.type = 'application/ld+json';
            const productsData = {
                "@context": "https://schema.org",
                "@type": "ItemList",
                "name": "Sản phẩm nổi bật tại ALORÉNA",
                "description": "Bộ sưu tập nước hoa chính hãng cao cấp",
                "itemListElement": seoData.productCategory.map((product, index) => ({
                    "@type": "ListItem",
                    "position": index + 1,
                    "item": {
                        "@type": "Product",
                        "name": product.meta_keywords,
                        "description": product.meta_description,
                        "image": product.image_url,
                        "offers": product?.offers
                    }
                }))
            };
            productsScript.textContent = JSON.stringify(productsData);
            document.head.appendChild(productsScript);
        }

        // Add breadcrumb for homepage
        const breadcrumbData = {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [{
                "@type": "ListItem",
                "position": 1,
                "name": "Trang chủ",
                "item": window.location.origin
            }]
        };

        const breadcrumbScript = document.createElement('script');
        breadcrumbScript.type = 'application/ld+json';
        breadcrumbScript.textContent = JSON.stringify(breadcrumbData);
        document.head.appendChild(breadcrumbScript);

        // Cleanup function
        return () => {
            // Optional cleanup
        };

    }, [seoData]);
};

export default useSEO;
