import React from 'react';
import {
  StyleSheet, View, Image,
} from 'react-native';
import {
  Text, Card, Checkbox, Pill, Select,
} from 'components';
import { scale, verticalScale } from 'react-native-size-matters';
import PropTypes from 'prop-types';
import Colors from 'themes/colors';
import { getProductById } from 'mocks/products';
import Header from './Header';

const styles = StyleSheet.create({
  card: {
    padding: scale(10),
    marginBottom: verticalScale(14),
  },
  imageContainer: {
    borderRadius: scale(8),
    overflow: 'hidden',
    width: scale(80),
    aspectRatio: 1 / 1,
    marginHorizontal: scale(14),
  },
  image: {
    flex: 1,
    width: undefined,
    height: undefined,
  },
  flex: {
    flex: 1,
  },
  content: {
    marginTop: scale(14),
    flexDirection: 'row',
    alignItems: 'center',
  },
  control: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingTop: verticalScale(14),
  },
  contentHeader: {
    flexDirection: 'row',
    padding: scale(14),
    borderBottomWidth: StyleSheet.hairlineWidth,
    borderColor: Colors.gray25,
  },
});

const CartList = ({ carts }) => (
  <>
    {carts.items.map((item) => (
      <Card style={styles.card} key={item.id}>
        <Header shop={item.shop} />
        {item.products.map((p) => {
          const product = getProductById(p.id);
          return (
            <View style={styles.content} key={product.id}>
              <Checkbox />
              <View style={styles.imageContainer}>
                <Image
                  source={product.images[0]}
                  style={styles.image}
                  resizeMode="cover"
                />
              </View>
              <View style={styles.flex}>
                <View style={styles.flex}>
                  <Text numberOfLines={1}>{product.name}</Text>
                  <Select
                    options={product.variants}
                    value={product.variants[0]}
                    optionTitle="Select options:"
                    renderContentHeader={() => (
                      <View style={styles.contentHeader}>
                        <View style={[styles.imageContainer, { marginLeft: 0 }]}>
                          <Image
                            source={product.images[0]}
                            style={styles.image}
                            resizeMode="cover"
                          />
                        </View>
                        <View>
                          <Text numberOfLines={1}>{product.name}</Text>
                          <Text flex color="primary" weight="medium" font="h2">{product.price}</Text>
                        </View>
                      </View>
                    )}
                  />
                </View>
                <View style={styles.control}>
                  <Text flex color="primary" weight="medium" font="h2">{product.price}</Text>
                  <Pill min={0} initialValue={p.quantity} />
                </View>
              </View>
            </View>
          );
        })}
      </Card>
    ))}
  </>
);

CartList.propTypes = {
  carts: PropTypes.object.isRequired,
};

export default CartList;
