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
For example, for Nuxt this might be added to a catch all route like so:

    // If no entry or page found
    if (data.entry == null) {
        let custom = await axios.post(process.env.cmsBaseUrl + '/rest/v1/redirect', qs.stringify({ uri: route.path }));
        if (custom && custom.data)
            redirect(custom.data.code, custom.data.to);
        
        throw ({ statusCode: 404, message: 'Page not found' });
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
    

## To Do

- Create CMS interface for CSV upload
- Add GraphQL query support