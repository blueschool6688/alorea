import DOMPurify from 'dompurify';

const HtmlContent = ({ content, className = "", maxLength = null, lineClamp = null }) => {
    console.log('Original content:', content);

    if (!content) {
        console.log('No content provided');
        return <div className={`html-content ${className}`}>No content available</div>;
    }

    const sanitizedContent = DOMPurify.sanitize(content, {
        ALLOWED_TAGS: ['p', 'br', 'strong', 'em', 'u', 'b', 'i', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'ul', 'ol', 'li', 'table', 'thead', 'tbody', 'tr', 'th', 'td',
            'blockquote', 'a', 'img', 'div', 'span'],
        ALLOWED_ATTR: ['href', 'src', 'alt', 'title', 'class', 'style', 'target']
    });

    // Debug: Log sanitized content
    console.log('Sanitized content:', sanitizedContent);

    if (!sanitizedContent || sanitizedContent.trim() === '') {
        console.log('Content was sanitized to empty');
        return <div className={`html-content ${className}`}>Content was filtered out</div>;
    }

    const style = lineClamp ? {
        display: '-webkit-box',
        WebkitLineClamp: lineClamp,
        WebkitBoxOrient: 'vertical',
        overflow: 'hidden',
        textOverflow: 'ellipsis'
    } : {};

    return (
        <div
            className={`html-content ${className}`}
            style={style}
            dangerouslySetInnerHTML={{ __html: sanitizedContent }}
        />
    );
};

export default HtmlContent;
