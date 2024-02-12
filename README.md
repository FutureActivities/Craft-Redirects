# Craft Custom Redirects

This provides a basic area of the CMS to define custom redirects.
Requires **Craft v4+**

This plugin **does nothing on the frontend**, instead it only provides the following
REST endpoint to lookup if a redirect exists:

    /rest/v1/redirect
    
Which expects a body param of:

    uri = the full path, e.g. /mortgages

regex is supported in the from field. Examples:

    ^/stansted?
    ^/(estate-agents/)?(\bedinburghwest\b)(/.*)?(/)?
    ^/estate-agents/(.*)/buying/?
    
## Example Usage

The plugin will respond in the following format:

    {
        "from": "/one",
        "to": "/two",
        "code": "301"
    }


You can then integrate this into your chosen frontend system however you require.
For example, for Nuxt 3 this might be added to a catch all route like so:

    const { data } = await useAsyncQuery(Url, { siteId: config.public.siteId, url: route.path.replace(/^\/+/g, '') });

    // check if entry is null and trigger 404 error
    if (!data.value || !data.value.entry) {
        // Check for a redirect
        const custom = await axios.post(config.public.cmsUrl + '/rest/v1/redirect', qs.stringify({ uri: route.path }));
        if (custom && custom.data)
            await navigateTo(custom.data.to, { redirectCode: custom.data.code })
        
        throw createError({ statusCode: 404, statusMessage: 'Page not found' });
    }


## Import

A CSV of redirects can be imported by uploading the CSV to:
`/storage/redirect-import.csv`

The CSV should be in this format;

    from, to, group, code, siteId
    
Where:

    group = A free text grouping of the redirects in the CMS
    code = The status code, likely either 301 or 302
    
Once uploaded, navigate to the following url in the CMS:

    /admin/customredirects/import
    

## Multisite Support

This plugin supports multiple sites.
When calling the endpoint, set the CMS base url to include your site handle.
For example:

    /de/rest/v1/redirect


## To Do

- Create CMS interface for CSV upload
- Consider GraphQL query support