import LayoutHorizontal from "@/layouts/LayoutVertical";
import { SettingsProvider } from "@/lib/context/SettingContext";
import Register from "@/pages/auth/Register";
import GeneralPath from "@/routes/GeneralPath";
import GeneralRoute from "@/routes/GeneralRoute";
import '@ant-design/v5-patch-for-react-19';
import '@fontsource/instrument-sans/400.css';
import '@fontsource/instrument-sans/500.css'; // Optional
import '@fontsource/instrument-sans/600.css';
import '@fontsource/instrument-sans/700.css';
import '@fontsource/merriweather/400.css';
import '@fontsource/merriweather/700.css';
import { createBrowserRouter, RouterProvider } from "react-router-dom";
import "slick-carousel/slick/slick-theme.css";
import "slick-carousel/slick/slick.css";
import '../css/app.css';


function App() {
    const router = createBrowserRouter([
        {
            element: <LayoutHorizontal />,
            children: GeneralRoute
        },
        {
            path: GeneralPath.REGISTER,
            element: <Register />
        },
    ]);

    return (
        <SettingsProvider>
            <RouterProvider router={router} />
        </SettingsProvider>
    );
}
export default App;
