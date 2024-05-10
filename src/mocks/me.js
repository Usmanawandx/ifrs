const me = {
  name: 'John Doe',
  username: '@JohnDoe',
  email: 'john@doe.com',
  image: require('images/users/7.jpg'),
  cards: [
    {
      bankName: 'City Ward Banks',
      cardType: 'visa',
      cardNumber: '4539 3849 2819 5492',
      cardNumberMasked: '45** **** **** 5492',
      cardholderName: 'John Doe',
      expiry: '11/23',
      cvv: '135',
    },
    {
      bankName: 'One Duty Banks',
      cardType: 'mastercard',
      cardNumber: '5639 2453 5553 5231',
      cardNumberMasked: '56** **** **** 5231',
      cardholderName: 'John Doe',
      expiry: '04/25',
      cvv: '188',
    },
  ],
  addresses: [
    {
      name: 'John Doe',
      phoneNumber: '+1-202-555-0163',
      address: '2757 Browning Lane, 53718 Madison, Wisconsin',
      street: '2757 Browning Lane',
      postalCode: '53718',
      city: 'Madison',
      state: 'Wisconsin',
      isDefault: true,
      type: 'home',
    },
    {
      name: 'Gregory Mattingly',
      phoneNumber: '+269-962-2468',
      address: '1110  Burwell Heights Road, 77701 Beaumont, Texas',
      street: '1110  Burwell Heights Road',
      postalCode: '77701',
      city: 'Beaumont',
      state: 'Texas',
      isDefault: false,
      type: 'school',
    },
    {
      name: 'John Doe',
      phoneNumber: '+1-202-555-0163',
      address: '679  Echo Lane, 53718 Lakeland, Florida',
      street: '679  Echo Lane',
      postalCode: '33807',
      city: 'Lakeland',
      state: 'Florida',
      isDefault: false,
      type: 'office',
    },
  ],
};

export const getProfile = () => me;
export const getDefaultAddress = () => me.addresses.find((address) => address.isDefault);
