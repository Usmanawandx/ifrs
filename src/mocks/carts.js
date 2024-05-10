const carts = {
  items: [
    {
      id: 1,
      shop: {
        id: 3,
        name: 'The Old Hunter\'s Club',
        image: require('images/shops/3.jpg'),
      },
      shippingFee: '$10',
      subtotal: '$110',
      shopPromo: '- $10',
      products: [
        {
          id: 3,
          quantity: 2,
        },
        {
          id: 7,
          quantity: 4,
        },
      ],
    },
    {
      id: 2,
      shop: {
        id: 2,
        name: 'Vintage Fashion',
        image: require('images/shops/2.jpg'),
      },
      shippingFee: '$14',
      subtotal: '$539',
      shopPromo: null,
      products: [
        {
          id: 9,
          quantity: 1,
        },
        {
          id: 10,
          quantity: 4,
        },
        {
          id: 5,
          quantity: 1,
        },
      ],
    },
  ],
  subtotal: '$649',
  shippingFee: '$24',
  total: '$653.00',
};

export const getCarts = () => carts;
