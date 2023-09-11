import {authGuard} from "@auth0/auth0-vue";
import {RouteRecordRaw} from "vue-router";

export default [
    {
        path: '/',
        name: 'home',
        component: () => import('~/components/Index.vue'),
        meta: {
            title: 'Home'
        }
    },
    {
        path: '/about',
        name: 'about',
        component: () => import('~/components/About.vue'),
        meta: {
            title: 'About FurryMusicDB',
        }
    },
    {
        path: '/help',
        name: 'help',
        component: () => import('~/components/Help.vue'),
        meta: {
            title: 'Help'
        }
    },
    {
        path: '/reviews',
        name: 'reviews',
        component: () => import('~/components/Reviews.vue'),
        meta: {
            title: 'Reviews'
        }
    },
    {
        path: '/donate',
        name: 'donate',
        component: () => import('~/components/Donate.vue'),
        meta: {
            title: 'Donate'
        }
    },
    {
        path: '/labels',
        name: 'labels',
        component: () => import('~/components/Labels.vue'),
        meta: {
            title: 'Labels'
        }
    },
    {
        path: '/label/:label_id',
        name: 'label',
        component: () => import('~/components/Label.vue'),
        meta: {
            title: 'View Label',
            breadcrumb: [
                {
                    label: 'Labels',
                    url: {
                        name: 'labels'
                    }
                }
            ]
        }
    },
    {
        path: '/artists',
        name: 'artists',
        component: () => import('~/components/Artists.vue'),
        meta: {
            title: 'Musicians'
        }
    },
    {
        path: '/artist/:artist_id',
        name: 'artist',
        component: () => import('~/components/Artist.vue'),
        meta: {
            title: 'Musician',
            breadcrumb: [
                {
                    label: 'Musicians',
                    url: {
                        name: 'artists'
                    }
                }
            ]
        }
    },
    {
        path: '',
        beforeEnter: authGuard,
        children: [
            {
                path: '/profile',
                name: 'profile',
                component: () => import('~/components/Profile.vue'),
                meta: {
                    title: 'My Profile',
                }
            },
            {
                path: '',
                meta: {
                    breadcrumb: [
                        {
                            label: 'My Profile',
                            url: {
                                name: 'profile'
                            }
                        }
                    ]
                },
                children: [
                    {
                        path: '/labels/create',
                        name: 'profile:label:create',
                        component: () => import('~/components/Profile/EditLabel.vue'),
                        meta: {
                            title: 'Create New Label'
                        }
                    },
                    {
                        path: '/label/:label_id/edit',
                        name: 'profile:label:edit',
                        component: () => import('~/components/Profile/EditLabel.vue'),
                        meta: {
                            title: 'Edit Label',
                        }
                    },
                    {
                        path: '/label/:label_id/artists',
                        name: 'profile:label:artists',
                        component: () => import('~/components/Profile/LabelArtists.vue'),
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
                                        name: 'profile:label:artists',
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
                                name: 'profile:label:artist:create',
                                component: () => import('~/components/Profile/EditArtist.vue'),
                                meta: {
                                    title: 'Create New Artist in Label',
                                },
                            },
                            {
                                path: '/label/:label_id/artist/:artist_id/edit',
                                name: 'profile:label:artist:edit',
                                component: () => import('~/components/Profile/EditArtist.vue'),
                                meta: {
                                    title: 'Edit Artist in Label',
                                },
                            }
                        ]
                    },
                    {
                        path: '/artists/create',
                        name: 'profile:artist:create',
                        component: () => import('~/components/Profile/EditArtist.vue'),
                        meta: {
                            title: 'Create New Artist'
                        }
                    },
                    {
                        path: '/artist/:artist_id/edit',
                        name: 'profile:artist:edit',
                        component: () => import('~/components/Profile/EditArtist.vue'),
                        meta: {
                            title: 'Edit Artist'
                        }
                    }
                ],
            }
        ]
    }
] as RouteRecordRaw[];
