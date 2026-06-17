import { message } from "antd";
export default function showMessage(content, type = 'success') {
    switch (type) {
        case 'success':
            message.success(content);
            break;
        case 'error':
            message.error(content)
            break;
        case 'warning':
            message.warning(content);
            break;
        case 'info':
            message.info(content);
            break;
        case 'loading':
            message.loading(content);
            break;
        default:
            message.info(content);
    }
};
