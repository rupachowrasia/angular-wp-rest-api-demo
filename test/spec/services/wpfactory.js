'use strict';

describe('Service: wpFactory', function () {

  // load the service's module
  beforeEach(module('devdacticWordpressApp'));

  // instantiate service
  var wpFactory;
  beforeEach(inject(function (_wpFactory_) {
    wpFactory = _wpFactory_;
  }));

  it('should do something', function () {
    expect(!!wpFactory).toBe(true);
  });

});
