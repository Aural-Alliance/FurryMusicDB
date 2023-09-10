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
        path: '/donate',
        name: 'donate',
        component: () => import('~/components/Donate.vue'),
        meta: {
            title: 'Donate'
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
                    },
                    {
                        path: '/artist/:artist_id/albums',
                        name: 'profile:artist:albums',
                        component: () => import('~/components/Profile/Albums.vue'),
                        meta: {
                            title: 'Manage Albums'
                        }
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
                                    label: 'Manage Albums',
                                    url: {
                                        name: 'profile:artist:albums',
                                        params: {
                                            artist_id: route.params.artist_id
                                        }
                                    }
                                }
                            ])
                        },
                        children: [
                            {
                                path: '/artist/:artist_id/albums/create',
                                name: 'profile:artist:album:create',
                                component: () => import('~/components/Profile/EditAlbum.vue'),
                                meta: {
                                    title: 'Create New Album'
                                }
                            },
                            {
                                path: '/artist/:artist_id/album/:album_id/edit',
                                name: 'profile:artist:album:edit',
                                component: () => import('~/components/Profile/EditAlbum.vue'),
                                meta: {
                                    title: 'Edit Album'
                                }
                            },
                            {
                                path: '/artist/:artist_id/album/:album_id/tracks',
                                name: 'profile:artist:album:tracks',
                                component: () => import('~/components/Profile/Tracks.vue'),
                                meta: {
                                    title: 'Manage Tracks'
                                }
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
                                            label: 'Manage Albums',
                                            url: {
                                                name: 'profile:artist:albums',
                                                params: {
                                                    artist_id: route.params.artist_id
                                                }
                                            }
                                        },
                                        {
                                            label: 'Album',
                                            url: {
                                                name: 'profile:artist:album:tracks',
                                                params: {
                                                    artist_id: route.params.artist_id,
                                                    album_id: route.params.album_id
                                                }
                                            }
                                        }
                                    ])
                                },
                                children: [
                                    {
                                        path: '/artist/:artist_id/album/:album_id/tracks/create',
                                        name: 'profile:artist:album:track:create',
                                        component: () => import('~/components/Profile/EditTrack.vue'),
                                        meta: {
                                            title: 'Add New Track'
                                        }
                                    },
                                    {
                                        path: '/artist/:artist_id/album/:album_id/track/:track_id/edit',
                                        name: 'profile:artist:album:track:edit',
                                        component: () => import('~/components/Profile/EditTrack.vue'),
                                        meta: {
                                            title: 'Edit Track'
                                        }
                                    },
                                ]
                            }
                        ]
                    }
                ],
            }
        ]
    }
] as RouteRecordRaw[];
