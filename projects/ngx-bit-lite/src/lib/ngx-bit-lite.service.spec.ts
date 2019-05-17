import { TestBed } from '@angular/core/testing';

import { NgxBitLiteService } from './ngx-bit-lite.service';

describe('NgxBitLiteService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: NgxBitLiteService = TestBed.get(NgxBitLiteService);
    expect(service).toBeTruthy();
  });
});
