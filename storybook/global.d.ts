declare module '*.twig' {
    export default function template(object: {[key: string]: any}): Promise<string>;
}
