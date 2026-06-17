# FilterSidebar Component

## Overview
A professional, reusable filter component designed for e-commerce product filtering with modern UI/UX features.

## Features

### ✨ Modern Design
- **Professional styling** with gradient backgrounds and smooth animations
- **Responsive design** that adapts to desktop and mobile layouts
- **Custom checkbox designs** with ripple effects and hover animations
- **Collapsible sections** with smooth expand/collapse animations

### 🎯 Advanced Functionality
- **Multiple selection filters** using checkboxes instead of radio buttons
- **Real-time filtering** with instant updates
- **Active filter counter** with visual badges
- **Smart clear filters** functionality
- **Product count indicators** for each filter option

### 📱 Mobile-First Approach
- **Mobile modal** with slide-up animation
- **Touch-friendly** interface with adequate spacing
- **Responsive grid layouts** for mobile filter options
- **Mobile-specific action buttons** (Apply/Reset)

### 🎨 Visual Features
- **Custom scrollbar** styling for better aesthetics
- **Gradient hover effects** and smooth transitions
- **Animated badges** for active filter counts
- **Professional color scheme** matching ALORÉA branding
- **Micro-interactions** with scale and fade animations

## Props

| Prop | Type | Required | Description |
|------|------|----------|-------------|
| `filters` | Object | ✅ | Current filter state object |
| `onFilterChange` | Function | ✅ | Callback for filter changes |
| `onClearFilters` | Function | ✅ | Callback to clear all filters |
| `isMobile` | Boolean | ❌ | Whether to render mobile version |
| `onClose` | Function | ❌ | Callback for mobile modal close |

## Filter Structure

```javascript
const filters = {
    gender: [],      // Array of selected gender values
    scent: [],       // Array of selected scent values
    priceRange: [],  // Array of selected price ranges
    searchTerm: ''   // Search term string
};
```

## Usage Example

```jsx
import FilterSidebar from '../../components/FilterSidebar';

const [filters, setFilters] = useState({
    gender: [],
    scent: [],
    priceRange: [],
    searchTerm: ''
});

const handleFilterChange = (filterType, value, checked) => {
    // Handle checkbox filter changes
    setFilters(prev => {
        const currentValues = prev[filterType] || [];
        let newValues;

        if (checked) {
            newValues = [...currentValues, value];
        } else {
            newValues = currentValues.filter(item => item !== value);
        }

        return {
            ...prev,
            [filterType]: newValues
        };
    });
};

const clearFilters = () => {
    setFilters({
        gender: [],
        scent: [],
        priceRange: [],
        searchTerm: ''
    });
};

// Desktop usage
<FilterSidebar
    filters={filters}
    onFilterChange={handleFilterChange}
    onClearFilters={clearFilters}
/>

// Mobile usage
<FilterSidebar
    filters={filters}
    onFilterChange={handleFilterChange}
    onClearFilters={clearFilters}
    isMobile={true}
    onClose={() => setShowMobileFilter(false)}
/>
```

## CSS Dependencies

The component requires these CSS classes defined in `app.css`:
- `.animate-slide-up` - Mobile modal animation
- `.animate-fade-in-scale` - Filter item animations
- `.animate-pulse-subtle` - Active filter badge animation
- `.filter-scrollbar` - Custom scrollbar styling
- `.checkbox-ripple` - Checkbox click effect

## Filter Categories

### Gender Options
- Nam (Men)
- Nữ (Women)
- Unisex

### Scent Categories
- Hoa (Floral)
- Gỗ (Woody)
- Ngọt (Sweet)
- Tươi mát (Fresh)
- Phương Đông (Oriental)
- Cam chanh (Citrus)

### Price Ranges
- 0đ - 500k
- 500k - 1tr
- 1tr - 2tr
- Trên 2tr

## Accessibility Features
- **Screen reader support** with proper ARIA labels
- **Keyboard navigation** for all interactive elements
- **High contrast** color combinations
- **Focus management** with visible focus indicators

## Performance Optimizations
- **Smooth animations** with hardware acceleration
- **Optimized re-renders** with proper state management
- **Lazy loading** of filter options
- **Debounced search** functionality (when integrated)

## Browser Compatibility
- ✅ Chrome 80+
- ✅ Firefox 75+
- ✅ Safari 13+
- ✅ Edge 80+
