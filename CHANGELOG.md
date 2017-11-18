# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

<!-- ## [Unreleased] -->

## [1.0.1] - 2017-11-18
### Added
- New discord command: "!santa mark" which will mark the channel it is used in as the announcement channel
- New artisan command: "reset:channel" which will remove the marked announcement channel
- At a configured date the bot starts automatically if the announcement channel has been set

### Changed
- Refactored existing discord code to use [Restcoord](https://github.com/restcord/restcord) instead of custom implementation

### Removed
- Removed "!santa start" command (see !santa mark)

## 1.0.0 - 2017-11-16
### Added
- Participants can register / unregister from the game
- Discord users can request a list of all participants
- The bot can be started manually
- At a configured date and time the participation time will end
- At a configured date and time the participants will get a random partner
- At a configured date and time a message will be sent to all participants with the information that they should now send their presents
- At new year a nice message will be posted
- Two days after new year the bot will clean up the announcement message

[1.0.1]: https://github.com/jkniest/secret-santa/compare/1.0.0...1.0.1
