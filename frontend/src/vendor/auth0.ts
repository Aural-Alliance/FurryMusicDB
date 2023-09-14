import {createAuth0} from "@auth0/auth0-vue";
import authConfig from "../../../auth_config.json";

const client = createAuth0({
    domain: authConfig.domain,
    clientId: authConfig.clientId,
    authorizationParams: {
        audience: authConfig.audience,
        redirect_uri: window.location.origin,
    },
    cacheLocation: 'localstorage'
});

export default client;

export const getAccessToken = async () => {
    return await client.getAccessTokenSilently()
};

export const isAdministrator = () => {

};
