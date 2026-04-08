declare module 'vuetify' {
    export * from 'vuetify/dist/vuetify'
}

declare module 'vuetify/components' {
    const components: Record<string, any>
    export default components
    export * from 'vuetify/dist/vuetify'
}

declare module 'vuetify/directives' {
    const directives: Record<string, any>
    export default directives
    export * from 'vuetify/dist/vuetify'
}