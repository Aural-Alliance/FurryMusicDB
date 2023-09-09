import {RouteRecordRaw} from "vue-router";

export default [
    {
        path: '/labels/create',
        name: 'label:create',
        component: () => import('~/components/Labels/EditLabel.vue'),
        meta: {
            title: 'Create New Label'
        }
    },
    {
        path: '/label/:label_id/edit',
        name: 'label:edit',
        component: () => import('~/components/Labels/EditLabel.vue'),
        meta: {
            title: 'Edit Label',
        }
    },
    {
        path: '/label/:label_id/artists',
        name: 'label:artists',
        component: () => import('~/components/Labels/Artists.vue'),
        meta: {
            title: 'Label Artists',
        },
    },
    {
        path: '',
        meta: {
            breadcrumb: (route) => ([
                {
                    label: 'My Profile',
                    url: {
                        name: 'profile'
                    }
                },
                {
                    label: 'Label Artists',
                    url: {
                        name: 'label:artists',
                        params: {
                            label_id: route.params.label_id
                        }
                    }
                }
            ])
        },
        children: [
            {
                path: '/label/:label_id/artists/create',
                name: 'label:artist:create',
                component: () => import('~/components/Artists/EditArtist.vue'),
                meta: {
                    title: 'Create New Artist in Label',
                },
            },
            {
                path: '/label/:label_id/artist/:artist_id/edit',
                name: 'label:artist:edit',
                component: () => import('~/components/Artists/EditArtist.vue'),
                meta: {
                    title: 'Edit Artist in Label',
                },
            }
        ]
    },
    {
        path: '/artists/create',
        name: 'artist:create',
        component: () => import('~/components/Artists/EditArtist.vue'),
        meta: {
            title: 'Create New Artist'
        }
    },
    {
        path: '/artist/:artist_id/edit',
        name: 'artist:edit',
        component: () => import('~/components/Artists/EditArtist.vue'),
        meta: {
            title: 'Edit Artist'
        }
    }
] as RouteRecordRaw[];
