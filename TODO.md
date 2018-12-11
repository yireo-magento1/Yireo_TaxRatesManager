# TODO
- Include composer library while packaging
- Test installation with and without composer

## Version 2.0
- Create proper transactional email
- Test what happens if somebody configures a feed that delivers 404 or 500 error
- Test what happens when Guzzle is of older version
- Test what happens if MagentoRates library is not available
- Add functional tests for:
    - Fixing dates automatically and see if this works
    - Removing all tax rates and restoring them

## Video tutorials
- Installing the extension via composer
    - Commit composer file
- Installing the extension manually
- Fixing rates manually
    - Change a date and see the hint in action
- Getting started without any rates
    - Demo environment, add dates automatically
- Fixing rates automatically
    - Via the backend
    - Via cron: `magerun sys:cron:run yireo_taxratesmanager`
- Testing if the New Year changes work
    - Change feed into something else