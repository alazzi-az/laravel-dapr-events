# Publisher Middleware

Default middleware pipeline (`config/dapr-events.php`):

| Middleware | Responsibility |
| --- | --- |
| `AddCorrelationId` | Propagates or generates a correlation identifier, adds it to PSR-3 context and Laravel's context facade. |
| `AddTenantContext` | Forwards tenant hints from the request header (`X-Tenant-ID`) or the authenticated user to Dapr metadata. |
| `AddTimestamp` | Adds the publication timestamp (`published_at`) in RFC3339 format. |

Add your own middleware by pushing classes into the `publisher.middleware` array. Each middleware receives an `EventContext` instance it can mutate before the final publish call.
