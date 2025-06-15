# Propel Assessment App
This is an assessment app, and a proof-of-concept. It's by no means production-ready!

## Architecture
The system follows a Domain-Driven Design architecture. Inside the services folder, you'll find the Address directory. This is the backbone of the app.
This is further subdivided into application layers:

### Domain
This contains our domain logic, and our domain objects.
At the time of writing, this only include the Address object itself. 
This is the abstraction we'll be passing around the system. It has both behaviour and state, and enforces its own coherency.

### Service
The service is a very thing layer that provides a coherent interface for other systems to hide behind, 
presenting a clear application boundary to the rest of the system. In this case, the service wraps both the ElasticSearch client and AddressRepository. In this way, we know every time a user calls the save against the repo, the index will also be updated.

### Infrastructure
This contains the nitty-gritty metal-facing code that's specific to our implementation.
At the moment, this contains the Address repository, which persists our state to JSON.

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
* Search
  * Elastic search now in place, need to clean up index when entry is deleted.
* ~~Alerts/Flash messages~~
* ~~Nicer styling~~ <- Not MVP, skipping for now.
* ~~WebComponent-powered Search~~
* ~~Error pages~~