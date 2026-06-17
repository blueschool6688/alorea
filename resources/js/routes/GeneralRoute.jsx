
import NotFound from "@/pages/404";
import Profile from "@/pages/auth/Profile";
import BlogDetail from "@/pages/BlogDetail";
import BlogPages from "@/pages/BlogPages";
import Checkout from "@/pages/Checkout";
import Collections from "@/pages/Collections";
import Homepage from "@/pages/Homepage";
import Introduce from "@/pages/Introduce";
import OrderPayment from "@/pages/OrderPayment";
import ProductDetail from "@/pages/Products/ProductDetail";
import ProductsLists from "@/pages/Products/ProductsLists";
import SearchOrder from "@/pages/SearchOrder";
import GeneralPath from "@/routes/GeneralPath";
const GeneralRoute = [
    { path: GeneralPath.HOMEPAGE, element: <Homepage /> },
    { path: GeneralPath.PRODUCTS, element: <ProductsLists /> },
    { path: GeneralPath.PRODUCT_DETAIL, element: <ProductDetail /> },
    { path: GeneralPath.COLLECTIONS, element: <Collections /> },
    { path: GeneralPath.BLOGS, element: <BlogPages /> },
    { path: GeneralPath.ABOUT, element: <Introduce /> },
    { path: GeneralPath.PROFILE, element: <Profile /> },
    { path: GeneralPath.CHECKOUT, element: <Checkout /> },
    { path: GeneralPath.ORDER_PAYMENT, element: <OrderPayment /> },
    { path: GeneralPath.BLOG_DETAIL, element: <BlogDetail /> },
    { path: GeneralPath.SEARCH_ORDER, element: <SearchOrder /> },

    { path: '*', element: <NotFound /> }
]

export default GeneralRoute;
