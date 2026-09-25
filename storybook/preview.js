import '../assets/css/250.less';

export const parameters = {
    actions: {argTypesRegex: '^on[A-Z].*'},
    controls: {
        disableSaveFromUI: true,
        matchers: {
            color: /(background|color)$/i,
            date: /Date$/,
        },
    },
    layout: 'centered',
};
