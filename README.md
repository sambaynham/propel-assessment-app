# Propel Assessment App
This is an assessment app, and a proof-of-concept. It's functional, but by no means production-ready!

## Technologies
* PHP/Laravel - Backend
* APIPlatform/Laravel - API. This technology provides full JSON+LD compliance out-of-the-box.
* ElasticSearch - Search Architecture
* Twig/Bootstrap/Sass - FrontEnd

## Architecture
The system follows a Domain-Driven Design architecture. Inside the services folder, you'll find the Address directory. This is the backbone of the app.
This is further subdivided into various layers:

### Application Layer
This layer contains application-layer adaptors. This contains no logic; it's responsible only for translating inputs into forms the Domain Service and Objects can understand. Adding new adaptors should, therefore, be easy.
### Domain Layer
This contains our domain logic, and our domain objects.
At the time of writing, this only includes the Address object itself. 
This is the abstraction we'll be passing around the system. It has both behaviour and state, and enforces its own coherency.

### Service Layer
The service is a very thin layer that provides a coherent interface for other systems to hide behind, 
presenting a clear application boundary to the rest of the system. 

In this case, the service wraps both the ElasticSearch client and AddressRepository. 

By doing this, we know every time a user calls the save against the repo, the index will also be updated.

### Infrastructure Layer
This contains the metal-facing code that's specific to our implementation.
This contains the Address repository, which persists our state to JSON.

Good to know: the repository itself is injected as a singleton; we always have the same instance.
This allows us to memoize our data. As always, memoization is a trade-off; we're trading space (memory) for time (reading and writing to memory is a lot faster than disk I/O).

This likely wouldn't be a good long-term solution, but that's no problem.

If we want to switch to a database-backed system later on, all we need to do is write an adaptor that obeys AddressRepositoryInterface and bind it in the container.
The rest of the system, loosely-coupled as it is, should just work.

## Notes
This is a Proof-of-concept only. It lacks certain key features you'd expect from a fully-fledged app:
* User registration
* RBAC

## To-dos
* ~~Delete address~~ 
* ~~API Implementation~~
* ~~Search~~
  * ~~Elastic search now in place, need to clean up index when entry is deleted.~~
* ~~Alerts/Flash messages~~
* ~~Nicer styling~~
* ~~WebComponent-powered Search~~
* ~~Error pages~~

## Future Ideas
* Replace memoized JSON address storage with ElasticSearch or similar as working memory.
  * Doing this would mean we only had a single source-of-truth within the application: the ElasticaIndex, which we would commit to Json as necessary.
  * It would also mean we're not so memory-dependent; now, a large collection would consume a large amount of system memory on a per-request basis. Fine for a PoC, not good for production.
* WebComponents-powered search frontend
  * The current search is functional, but old-fashioned. It posts to a controller endpoint (which passes through to the AddressService).
  * If we added a WebComponent, we could provide real-time results on any page we cared to.
* Replace ApiSearchController with queries on API.
  * The ApiSearchController is a necessary evil, due to time constraints. However, with some extra plumbing ApiPlatform integrates with ElasticSearch natively.
  * We could replace the current POST:/api/search with more standards-compliant get/address?query="Search Terms." 
  * Doing this would mean we could get rid of the search controller.