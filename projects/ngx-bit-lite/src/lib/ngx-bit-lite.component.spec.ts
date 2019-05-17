import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { NgxBitLiteComponent } from './ngx-bit-lite.component';

describe('NgxBitLiteComponent', () => {
  let component: NgxBitLiteComponent;
  let fixture: ComponentFixture<NgxBitLiteComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ NgxBitLiteComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(NgxBitLiteComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
